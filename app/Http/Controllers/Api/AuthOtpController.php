<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OtpCode;
use App\Models\User;
use App\Notifications\OtpCodeNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthOtpController extends Controller
{
    private const OTP_TTL_MINUTES = 10;

    public function signup(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'role' => ['nullable', 'string', 'max:100'],
        ]);

        $user = User::query()->create([
            'name' => $validated['name'],
            'last_name' => $validated['last_name'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'],
            'password' => $validated['password'],
            'is_verified' => false,
        ]);

        $roleName = $validated['role'] ?? 'user';
        if (Role::query()->where('name', $roleName)->exists()) {
            $user->assignRole($roleName);
        }

        $token = (string) JWTAuth::fromUser($user);

        return response()->json([
            'status' => true,
            'message' => 'User created successfully',
            'data' => [
                'token' => $token,
                'user' => $user,
            ],
        ], 201);
    }

    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'is_verified' => false,
        ]);

        $this->issueOtp($user->email, OtpCode::PURPOSE_REGISTER, $user->id);

        return response()->json([
            'status' => true,
            'message' => 'Registration successful. OTP sent to email.',
            'data' => [
                'email' => $user->email,
            ],
        ], 201);
    }

    public function verifyRegisterOtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'otp' => ['required', 'digits:6'],
        ]);

        $otp = $this->consumeOtp($validated['email'], OtpCode::PURPOSE_REGISTER, $validated['otp']);
        if (! $otp) {
            throw ValidationException::withMessages(['otp' => ['Invalid or expired OTP.']]);
        }

        $user = User::query()->where('email', $validated['email'])->firstOrFail();
        $user->forceFill([
            'email_verified_at' => now(),
            'otp_verified_at' => now(),
            'is_verified' => true,
        ])->save();

        $token = (string) JWTAuth::fromUser($user);

        return $this->tokenResponse($token);
    }

    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::query()->where('email', $validated['email'])->first();
        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages(['email' => ['Invalid credentials.']]);
        }

        $this->issueOtp($user->email, OtpCode::PURPOSE_LOGIN, $user->id);

        return response()->json([
            'status' => true,
            'message' => 'OTP sent to email. Verify OTP to receive JWT token.',
            'data' => [
                'email' => $user->email,
            ],
        ]);
    }

    public function loginPassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::guard('api')->attempt(['email' => $validated['email'], 'password' => $validated['password']])) {
            throw ValidationException::withMessages(['email' => ['Invalid email or password.']]);
        }

        $user = Auth::guard('api')->user();
        $token = (string) JWTAuth::fromUser($user);

        return response()->json([
            'status' => true,
            'message' => 'Login successfully',
            'data' => [
                'token' => $token,
                'user' => $user,
            ],
        ]);
    }

    public function verifyLoginOtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'otp' => ['required', 'digits:6'],
        ]);

        $otp = $this->consumeOtp($validated['email'], OtpCode::PURPOSE_LOGIN, $validated['otp']);
        if (! $otp) {
            throw ValidationException::withMessages(['otp' => ['Invalid or expired OTP.']]);
        }

        $user = User::query()->where('email', $validated['email'])->firstOrFail();
        $token = (string) JWTAuth::fromUser($user);

        return $this->tokenResponse($token);
    }

    public function requestPasswordResetOtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $user = User::query()->where('email', $validated['email'])->firstOrFail();
        $this->issueOtp($user->email, OtpCode::PURPOSE_PASSWORD_RESET, $user->id);

        return response()->json([
            'status' => true,
            'message' => 'Password reset OTP sent to email.',
            'data' => [
                'email' => $user->email,
            ],
        ]);
    }

    public function forgetPassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $user = User::query()->where('email', $validated['email'])->firstOrFail();
        $otpCode = (string) random_int(100000, 999999);

        $user->forceFill([
            'otp' => $otpCode,
            'otp_expires_at' => now()->addMinutes(2),
            'otp_verified_at' => null,
            'reset_password_token' => null,
            'reset_password_token_expires_at' => null,
        ])->save();

        Notification::route('mail', $user->email)
            ->notify(new OtpCodeNotification($otpCode, OtpCode::PURPOSE_PASSWORD_RESET, 2));

        return response()->json([
            'status' => true,
            'message' => 'OTP sent successfully',
            'data' => [
                'email' => $user->email,
                'otp_expires_at' => $user->otp_expires_at,
            ],
        ]);
    }

    public function checkOtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'otp' => ['required', 'digits:6'],
        ]);

        $user = User::query()
            ->where('email', $validated['email'])
            ->where('otp', $validated['otp'])
            ->first();

        if (! $user) {
            throw ValidationException::withMessages(['otp' => ['Invalid OTP.']]);
        }

        if (! $user->otp_expires_at || now()->gt($user->otp_expires_at)) {
            throw ValidationException::withMessages(['otp' => ['OTP expired.']]);
        }

        $user->forceFill([
            'otp_verified_at' => now(),
            'email_verified_at' => $user->email_verified_at ?? now(),
            'reset_password_token' => Str::random(60),
            'reset_password_token_expires_at' => now()->addMinutes(5),
        ])->save();

        return response()->json([
            'status' => true,
            'message' => 'OTP verified successfully',
            'data' => [
                'email' => $user->email,
                'reset_password_token' => $user->reset_password_token,
                'reset_password_token_expires_at' => $user->reset_password_token_expires_at,
            ],
        ]);
    }

    public function resetPasswordWithOtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'otp' => ['required', 'digits:6'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $otp = $this->consumeOtp($validated['email'], OtpCode::PURPOSE_PASSWORD_RESET, $validated['otp']);
        if (! $otp) {
            throw ValidationException::withMessages(['otp' => ['Invalid or expired OTP.']]);
        }

        $user = User::query()->where('email', $validated['email'])->firstOrFail();
        $user->forceFill([
            'password' => $validated['password'],
            'otp' => null,
            'otp_expires_at' => null,
            'otp_verified_at' => null,
            'reset_password_token' => null,
            'reset_password_token_expires_at' => null,
        ])->save();

        return response()->json([
            'status' => true,
            'message' => 'Password reset successful.',
            'data' => [
                'email' => $user->email,
            ],
        ]);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'reset_password_token' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::query()
            ->where('email', $validated['email'])
            ->where('reset_password_token', $validated['reset_password_token'])
            ->whereNotNull('otp_verified_at')
            ->first();

        if (! $user) {
            throw ValidationException::withMessages(['reset_password_token' => ['Please try again.']]);
        }

        if (! $user->reset_password_token_expires_at || now()->gt($user->reset_password_token_expires_at)) {
            throw ValidationException::withMessages(['reset_password_token' => ['Token expired.']]);
        }

        $user->forceFill([
            'password' => $validated['password'],
            'otp' => null,
            'otp_expires_at' => null,
            'otp_verified_at' => null,
            'reset_password_token' => null,
            'reset_password_token_expires_at' => null,
        ])->save();

        return response()->json([
            'status' => true,
            'message' => 'Password reset successfully',
            'data' => [
                'email' => $user->email,
            ],
        ]);
    }

    public function resendOtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'purpose' => ['nullable', 'string', 'in:register,login,password_reset'],
        ]);

        $user = User::query()->where('email', $validated['email'])->firstOrFail();
        $purpose = $validated['purpose'] ?? OtpCode::PURPOSE_PASSWORD_RESET;

        if ($purpose === OtpCode::PURPOSE_PASSWORD_RESET) {
            $otpCode = (string) random_int(100000, 999999);
            $user->forceFill([
                'otp' => $otpCode,
                'otp_expires_at' => now()->addSeconds(30),
            ])->save();

            Notification::route('mail', $user->email)
                ->notify(new OtpCodeNotification($otpCode, OtpCode::PURPOSE_PASSWORD_RESET, 1));
        }

        $this->issueOtp($user->email, $purpose, $user->id);

        return response()->json([
            'status' => true,
            'message' => 'OTP resent successfully.',
            'data' => [
                'email' => $user->email,
                'purpose' => $purpose,
            ],
        ]);
    }

    public function me(): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'User profile',
            'data' => Auth::guard('api')->user()?->loadMissing(['roles', 'permissions']),
        ]);
    }

    public function refresh(): JsonResponse
    {
        return $this->tokenResponse((string) JWTAuth::parseToken()->refresh());
    }

    public function logout(): JsonResponse
    {
        Auth::guard('api')->logout();

        return response()->json([
            'status' => true,
            'message' => 'Successfully logged out',
            'data' => (object) [],
        ]);
    }

    public function changePassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = Auth::guard('api')->user();
        if (! $user || ! Hash::check($validated['current_password'], $user->password)) {
            throw ValidationException::withMessages(['current_password' => ['Current password does not match.']]);
        }

        $user->update([
            'password' => $validated['new_password'],
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Password changed successfully',
            'data' => $this->userProfilePayload($user->fresh()),
        ]);
    }

    public function deleteAccount(Request $request): JsonResponse
    {
        $user = Auth::guard('api')->user();
        if (! $user) {
            throw ValidationException::withMessages(['token' => ['Invalid token or user not found.']]);
        }

        $user->forceFill([
            'email' => 'deleted_'.$user->id.'_'.time().'@deleted.local',
            'name' => 'Deleted User',
            'phone' => null,
        ])->save();

        $user->delete();

        return response()->json([
            'status' => true,
            'message' => 'Account deleted successfully',
            'data' => (object) [],
        ]);
    }

    public function userProfileImage(): JsonResponse
    {
        $user = Auth::guard('api')->user();
        if (! $user) {
            throw ValidationException::withMessages(['token' => ['Invalid token or user not found.']]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Profile image fetched successfully',
            'data' => [
                'profile_image' => $user->profile_image ? asset($user->profile_image) : null,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    public function switchAccount(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'string', 'exists:roles,name'],
        ]);

        $user = Auth::guard('api')->user();
        if (! $user) {
            throw ValidationException::withMessages(['token' => ['Invalid token or user not found.']]);
        }

        $targetRole = $validated['role'];

        if (! $user->hasRole($targetRole)) {
            $user->assignRole($targetRole);
        }

        if (! $user->hasRole('user') && Role::query()->where('name', 'user')->exists()) {
            $user->assignRole('user');
        }

        $token = JWTAuth::claims(['role' => $targetRole])->fromUser($user);

        return response()->json([
            'status' => true,
            'message' => 'Account switched successfully',
            'data' => [
                'token' => $token,
                'account_info' => $user->fresh()->loadMissing(['roles', 'permissions']),
            ],
        ]);
    }

    public function profileImageUpdate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'profile_image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user = Auth::guard('api')->user();
        if (! $user) {
            throw ValidationException::withMessages(['token' => ['Invalid token or user not found.']]);
        }

        $file = $validated['profile_image'];
        $fileName = time().'_'.$user->id.'.'.$file->getClientOriginalExtension();
        $path = 'uploads/profile_photo/';
        if (! is_dir(public_path($path))) {
            mkdir(public_path($path), 0777, true);
        }
        $file->move(public_path($path), $fileName);

        $user->update([
            'profile_image' => $path.$fileName,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Profile image updated successfully',
            'data' => $this->userProfilePayload($user->fresh()),
        ]);
    }

    public function userProfileGet(): JsonResponse
    {
        $user = Auth::guard('api')->user();
        if (! $user) {
            throw ValidationException::withMessages(['token' => ['Invalid token or user not found.']]);
        }

        return response()->json([
            'status' => true,
            'message' => 'User profile fetched successfully',
            'data' => $this->userProfilePayload($user),
        ]);
    }

    public function userProfileUpdate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        $user = Auth::guard('api')->user();
        if (! $user) {
            throw ValidationException::withMessages(['token' => ['Invalid token or user not found.']]);
        }

        $user->forceFill([
            'name' => $validated['first_name'] ?? $user->name,
            'last_name' => $validated['last_name'] ?? $user->last_name,
            'phone' => $validated['phone'] ?? $user->phone,
            'address' => $validated['address'] ?? $user->address,
        ])->save();

        return response()->json([
            'status' => true,
            'message' => 'Profile updated successfully',
            'data' => $this->userProfilePayload($user->fresh()),
        ]);
    }

    public function fcmToken(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'fcm_token' => ['required', 'string'],
        ]);

        $user = Auth::guard('api')->user();
        if (! $user) {
            throw ValidationException::withMessages(['token' => ['Invalid token or user not found.']]);
        }

        $user->update([
            'fcm_token' => $validated['fcm_token'],
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Fcm token updated successfully',
            'data' => [
                'id' => $user->id,
                'fcm_token' => $user->fcm_token,
            ],
        ]);
    }

    private function issueOtp(string $email, string $purpose, ?int $userId = null): void
    {
        OtpCode::query()
            ->where('email', $email)
            ->where('purpose', $purpose)
            ->whereNull('consumed_at')
            ->update(['consumed_at' => now()]);

        $otpCode = (string) random_int(100000, 999999);

        OtpCode::query()->create([
            'user_id' => $userId,
            'email' => $email,
            'purpose' => $purpose,
            'code_hash' => Hash::make($otpCode),
            'expires_at' => now()->addMinutes(self::OTP_TTL_MINUTES),
        ]);

        Notification::route('mail', $email)
            ->notify(new OtpCodeNotification($otpCode, $purpose, self::OTP_TTL_MINUTES));
    }

    private function consumeOtp(string $email, string $purpose, string $otpCode): ?OtpCode
    {
        $otp = OtpCode::query()
            ->where('email', $email)
            ->where('purpose', $purpose)
            ->whereNull('consumed_at')
            ->latest('id')
            ->first();

        if (! $otp || $otp->isExpired()) {
            return null;
        }

        if (! Hash::check($otpCode, $otp->code_hash)) {
            $otp->increment('attempts');

            return null;
        }

        $otp->update(['consumed_at' => now()]);

        return $otp;
    }

    private function tokenResponse(string $token): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'Authentication successful',
            'data' => [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => (int) config('jwt.ttl', 60) * 60,
                'user' => Auth::guard('api')->user()?->loadMissing(['roles', 'permissions']),
            ],
        ]);
    }

    private function userProfilePayload(User $user): array
    {
        return [
            'id' => $user->id,
            'first_name' => $user->name,
            'last_name' => $user->last_name,
            'image' => $user->profile_image ? asset($user->profile_image) : null,
            'email' => $user->email,
            'phone' => $user->phone,
            'address' => $user->address,
        ];
    }
}