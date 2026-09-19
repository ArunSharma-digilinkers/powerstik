<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function edit(): View
    {
        return view('admin.settings.edit', [
            'groups' => config('settings'),
            'values' => Setting::allValues(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $rules = [];

        foreach (config('settings') as $group) {
            foreach ($group['fields'] as $key => $field) {
                $rules[$key] = match ($field['type'] ?? 'text') {
                    'email' => ['nullable', 'email', 'max:255'],
                    'url' => ['nullable', 'url', 'max:1000'],
                    'number' => ['nullable', 'integer'],
                    'textarea' => ['nullable', 'string', 'max:5000'],
                    default => ['nullable', 'string', 'max:255'],
                };
            }
        }

        $rules['whatsapp'][] = 'regex:/^\d{8,15}$/';

        Setting::put($request->validate($rules));

        return back()->with('status', 'Settings saved.');
    }
}
