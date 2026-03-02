<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Helpers\CurrencyHelper;
use App\Models\NotificationSetting;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    /**
     * Display the settings form.
     */
    public function index(Request $request)
    {
        $user          = Auth::user();
        $currencies    = CurrencyHelper::getCurrencies();
        $notifSettings = (new NotificationService)->getSettings($user);
        $activeTab     = $request->query('tab', 'profile');

        return view('portal.settings.index', compact('user', 'currencies', 'notifSettings', 'activeTab'));
    }

    /**
     * Update user profile / business / preferences settings.
     */
    public function update(Request $request)
    {
        $user  = Auth::user();
        $rules = [];

        if ($request->has('name')) {
            $rules['name'] = ['required', 'string', 'max:255'];
        }

        if ($request->has('email')) {
            $rules['email'] = ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)];
        }

        if ($request->has('phone')) {
            $rules['phone'] = ['nullable', 'string', 'max:50'];
        }

        if ($request->has('business_name')) {
            $rules['business_name'] = ['nullable', 'string', 'max:255'];
        }

        if ($request->has('business_phone')) {
            $rules['business_phone'] = ['nullable', 'string', 'max:50'];
        }

        if ($request->has('tax_number')) {
            $rules['tax_number'] = ['nullable', 'string', 'max:100'];
        }

        if ($request->has('business_address')) {
            $rules['business_address'] = ['nullable', 'string'];
        }

        if ($request->has('currency_code')) {
            $rules['currency_code'] = ['required', 'string', 'size:3', Rule::in(array_keys(CurrencyHelper::getCurrencies()))];
        }

        if ($request->has('timezone')) {
            $rules['timezone'] = ['required', 'string'];
        }

        if (empty($rules)) {
            return redirect()->route('settings.index')->with('error', 'No fields to update.');
        }

        $validated = $request->validate($rules);
        $user->update($validated);

        return redirect()->route('settings.index')->with('success', 'Settings updated successfully!');
    }

    /**
     * Upload / replace the business logo.
     */
    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => ['required', 'image', 'mimes:png,jpg,jpeg,svg,gif', 'max:2048'],
        ]);

        $user = Auth::user();

        // Remove old logo if exists
        if ($user->logo_path && Storage::disk('public')->exists($user->logo_path)) {
            Storage::disk('public')->delete($user->logo_path);
        }

        $ext  = $request->file('logo')->getClientOriginalExtension();
        $path = $request->file('logo')->storeAs('logos', $user->id . '.' . $ext, 'public');

        $user->update(['logo_path' => $path]);

        return redirect()->route('settings.index', ['tab' => 'business'])
            ->with('success', 'Business logo updated successfully!');
    }

    /**
     * Remove the business logo.
     */
    public function removeLogo(Request $request)
    {
        $user = Auth::user();

        if ($user->logo_path && Storage::disk('public')->exists($user->logo_path)) {
            Storage::disk('public')->delete($user->logo_path);
        }

        $user->update(['logo_path' => null]);

        return redirect()->route('settings.index', ['tab' => 'business'])
            ->with('success', 'Business logo removed.');
    }

    /**
     * Save document template / branding preferences.
     */
    public function updateDocuments(Request $request)
    {
        $request->validate([
            'primary_color'   => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'accent_color'    => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'highlight_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'font'            => ['required', Rule::in(['sans', 'serif', 'mono'])],
            'layout'          => ['required', Rule::in(['classic', 'modern', 'minimal'])],
            'show_logo'       => ['nullable'],
            'footer_note'     => ['nullable', 'string', 'max:300'],
        ]);

        $user = Auth::user();
        $user->update([
            'doc_settings' => [
                'primary_color'   => $request->primary_color,
                'accent_color'    => $request->accent_color,
                'highlight_color' => $request->highlight_color,
                'font'            => $request->font,
                'layout'          => $request->layout,
                'show_logo'       => (bool) $request->show_logo,
                'footer_note'     => $request->footer_note ?? 'Thank you for your business.',
            ],
        ]);

        return redirect()->route('settings.index', ['tab' => 'documents'])
            ->with('success', 'Document template saved successfully!');
    }

    /**
     * Update only currency preference (quick AJAX action).
     */
    public function updateCurrency(Request $request)
    {
        $request->validate([
            'currency_code' => ['required', 'string', 'size:3', Rule::in(array_keys(CurrencyHelper::getCurrencies()))],
        ]);

        Auth::user()->update([
            'currency_code' => $request->currency_code,
        ]);

        return response()->json([
            'success'  => true,
            'message'  => 'Currency updated successfully!',
            'currency' => $request->currency_code,
            'symbol'   => CurrencyHelper::getSymbol($request->currency_code),
        ]);
    }

    /**
     * Toggle dark mode preference (AJAX).
     */
    public function updateDarkMode(Request $request)
    {
        $user = Auth::user();
        $user->update(['dark_mode' => $request->boolean('dark_mode')]);

        return response()->json(['success' => true, 'dark_mode' => (bool) $user->dark_mode]);
    }

    /**
     * Update notification preferences.
     */
    public function updateNotifications(Request $request)
    {
        $request->validate([
            'quote_expiring_days'   => ['required', 'integer', 'min:1', 'max:30'],
            'debt_due_days'         => ['required', 'integer', 'min:1', 'max:30'],
            'project_deadline_days' => ['required', 'integer', 'min:1', 'max:30'],
        ]);

        $boolFields = [
            'invoice_overdue_app', 'invoice_overdue_email',
            'quote_expiring_app',  'quote_expiring_email',
            'debt_due_app',        'debt_due_email',
            'savings_goal_app',    'savings_goal_email',
            'project_deadline_app','project_deadline_email',
            'budget_alert_app',    'budget_alert_email',
        ];

        $data = [];
        foreach ($boolFields as $field) {
            $data[$field] = $request->boolean($field);
        }

        $data['quote_expiring_days']   = (int) $request->quote_expiring_days;
        $data['debt_due_days']         = (int) $request->debt_due_days;
        $data['project_deadline_days'] = (int) $request->project_deadline_days;

        NotificationSetting::updateOrCreate(
            ['user_id' => Auth::id()],
            $data
        );

        return redirect()->route('settings.index', ['tab' => 'notifications'])
            ->with('success', 'Notification preferences saved!');
    }
}
