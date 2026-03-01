<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class SystemSettingController extends Controller
{
    public function index()
    {
        // Group settings for display: ['general' => [...], 'security' => [...], ...]
        $settingGroups = SystemSetting::orderBy('group')->orderBy('id')->get()->groupBy('group');

        return view('admin.settings.index', compact('settingGroups'));
    }

    public function update(Request $request)
    {
        $settings = SystemSetting::all();
        $changed  = [];

        foreach ($settings as $setting) {
            $key = $setting->key;

            if ($setting->type === 'boolean') {
                // Checkboxes don't submit when unchecked
                $newValue = $request->has($key) ? '1' : '0';
            } else {
                $newValue = $request->input($key, '');
            }

            // Basic validation
            if ($setting->type === 'integer') {
                $newValue = (string)(int) $newValue;
            }

            if ((string) $setting->value !== $newValue) {
                $changed[$key] = ['from' => $setting->value, 'to' => $newValue];
                $setting->value = $newValue;
                $setting->save();
            }
        }

        if (!empty($changed)) {
            ActivityLog::log(
                'admin.settings.updated',
                'Admin updated ' . count($changed) . ' system setting(s).',
                null,
                $changed
            );
        }

        return back()->with('success', 'System settings have been saved successfully.');
    }
}
