<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function smtp(): View
    {
        return view('Backend.settings.smtp', [
            'settings' => Setting::getGroup('smtp'),
        ]);
    }

    public function updateSmtp(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'mail_mailer' => ['required', 'string', 'max:50'],
            'mail_host' => ['required', 'string', 'max:255'],
            'mail_port' => ['required', 'integer'],
            'mail_username' => ['nullable', 'string', 'max:255'],
            'mail_password' => ['nullable', 'string', 'max:255'],
            'mail_encryption' => ['nullable', 'string', 'max:20'],
            'mail_from_address' => ['nullable', 'email', 'max:255'],
            'mail_from_name' => ['nullable', 'string', 'max:255'],
        ]);

        foreach ($validated as $key => $value) {
            Setting::setValue('smtp', $key, $value);
        }

        return back()->with('status', 'SMTP settings updated.');
    }

    public function website(): View
    {
        return view('Backend.settings.website', [
            'settings' => Setting::getGroup('website'),
        ]);
    }

    public function updateWebsite(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'site_tagline' => ['nullable', 'string', 'max:255'],
            'site_email' => ['nullable', 'email', 'max:255'],
            'site_phone' => ['nullable', 'string', 'max:50'],
            'site_address' => ['nullable', 'string', 'max:255'],
            'site_logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
        ]);

        if ($request->hasFile('site_logo')) {
            $oldLogo = Setting::getValue('website', 'site_logo');

           if($oldLogo && file_exists(public_path($oldLogo))) {
               unlink(public_path($oldLogo));
            }

            $file=$request->file('site_logo');
            $filename = time().'_'.$file->getClientOriginalName();
            $path = 'uploads/settings/';
            $file->move(public_path($path), $filename);
            Setting::setValue('website', 'site_logo', $path . $filename);
        }

        unset($validated['site_logo']);

        foreach ($validated as $key => $value) {
            Setting::setValue('website', $key, $value);
        }

        return back()->with('status', 'Website settings updated.');
    }

    public function admin(): View
    {
        return view('Backend.settings.admin', [
            'settings' => Setting::getGroup('admin'),
        ]);
    }

    public function updateAdmin(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'timezone' => ['required', 'string', 'max:120'],
            'date_format' => ['required', 'string', 'max:30'],
            'maintenance_mode' => ['nullable', 'in:0,1'],
            'items_per_page' => ['required', 'integer', 'min:5', 'max:200'],
        ]);

        $validated['maintenance_mode'] = $request->boolean('maintenance_mode') ? '1' : '0';

        foreach ($validated as $key => $value) {
            Setting::setValue('admin', $key, (string) $value);
        }

        return back()->with('status', 'Admin settings updated.');
    }

    public function stripe(): View
    {
        return view('Backend.settings.stripe', [
            'settings' => Setting::getGroup('stripe'),
        ]);
    }

    public function updateStripe(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'stripe_status' => ['nullable', 'in:0,1'],
            'stripe_publishable_key' => ['nullable', 'string', 'max:255'],
            'stripe_secret_key' => ['nullable', 'string', 'max:255'],
            'stripe_webhook_secret' => ['nullable', 'string', 'max:255'],
            'stripe_currency' => ['required', 'string', 'max:10'],
        ]);

        $validated['stripe_status'] = $request->boolean('stripe_status') ? '1' : '0';
        $validated['stripe_currency'] = strtoupper((string) $validated['stripe_currency']);

        foreach ($validated as $key => $value) {
            Setting::setValue('stripe', $key, (string) $value);
        }

        return back()->with('status', 'Stripe settings updated.');
    }

    public function dynamicPage(): View
    {
        return view('Backend.settings.dynamic_page', [
            'settings' => Setting::getGroup('dynamic_page'),
        ]);
    }

    public function updateDynamicPage(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'dynamic_page_title' => ['required', 'string', 'max:255'],
            'dynamic_page_description' => ['nullable', 'string'],
        ]);

        foreach ($validated as $key => $value) {
            Setting::setValue('dynamic_page', $key, (string) $value);
        }

        return back()->with('status', 'Dynamic page content updated.');
    }
}
