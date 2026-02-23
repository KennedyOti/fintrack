<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Helpers\CurrencyHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    /**
     * Display the settings form.
     */
    public function index()
    {
        $user = Auth::user();
        $currencies = CurrencyHelper::getCurrencies();
        
        return view('portal.settings.index', compact('user', 'currencies'));
    }

    /**
     * Update user profile settings.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        // Build validation rules dynamically based on what fields are present
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
        
        // If no specific fields are being updated, require at least one
        if (empty($rules)) {
            return redirect()->route('settings.index')->with('error', 'No fields to update.');
        }
        
        $validated = $request->validate($rules);
        
        $user->update($validated);
        
        return redirect()->route('settings.index')->with('success', 'Settings updated successfully!');
    }

    /**
     * Update only currency preference (quick action).
     */
    public function updateCurrency(Request $request)
    {
        $request->validate([
            'currency_code' => ['required', 'string', 'size:3', Rule::in(array_keys(CurrencyHelper::getCurrencies()))],
        ]);
        
        Auth::user()->update([
            'currency_code' => $request->currency_code
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Currency updated successfully!',
            'currency' => $request->currency_code,
            'symbol' => CurrencyHelper::getSymbol($request->currency_code)
        ]);
    }
}
