<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TemplateController extends Controller
{
    public function index()
    {
        $settings = Setting::where('group', 'welcome')->get()->keyBy('key');
        return view('admin.template-customization', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->except(['_token']);

        foreach ($data as $key => $value) {
            if (str_starts_with($key, 'welcome_')) {
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value, 'group' => 'welcome']
                );
                Cache::forget("setting_{$key}");
            }
        }

        return back()->with('success', 'Welcome page texts updated successfully!');
    }
}
