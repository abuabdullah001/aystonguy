<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
           $table->id();
            $table->string('name')->comment('User full name');
            $table->string('username')->unique()->nullable()->comment('User username');

            $table->string('last_name')->nullable();
             $table->dateTime('dob')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('profile_image')->nullable();
            // $table->enum('role',['superadmin','admin', 'shop','user'])->default('user')->comment('User role: superadmin, admin, shop, user');
            // $table->string('shop_user')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('fcm_token')->nullable();
            $table->enum('shop_status', ['approve', 'pending', 'cancel'])->nullable();
            // $table->string('street')->nullable();
            // $table->string('city')->nullable();
            // $table->string('flat_house_no')->nullable();
            $table->string('post_code')->nullable();
            $table->boolean('is_agree')->default(1);

            $table->string('google_id')->nullable();
            $table->string('facebook_id')->nullable();
            $table->string('apple_id')->nullable();
            $table->string('reset_password_token')->nullable();
            $table->dateTime('reset_password_token_expires_at')->nullable();
            $table->decimal('latitude', 10, 8)->nullable()->comment('User GPS latitude');
            $table->decimal('longitude', 11, 8)->nullable()->comment('User GPS longitude');
            $table->string('otp')->nullable();
            $table->dateTime('otp_expires_at')->nullable();
            $table->dateTime('otp_verified_at')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->longText('account_delete_comment')->nullable();
            $table->longText('account_delete_reason')->nullable();

            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
