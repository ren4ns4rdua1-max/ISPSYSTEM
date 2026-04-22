<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TemplateController extends Controller
{
    public function index()
    {
        // Check if user is admin
// Admin check removed for all authenticated users
// if (!Auth::user()->is_admin) {
//     abort(403, 'Admin access required.');
// }


$settings = Setting::where('group', 'welcome')->get()->keyBy('key');

        return view('admin.template-customization', compact('settings'));
    }

    public function update(Request $request)
    {
        if (!Auth::user()->is_admin) {
            abort(403);
        }

        $request->validate([
            '*' => 'required|string|max:65535',
        ]);

        foreach ($request->all() as $key => $value) {
            if (str_starts_with($key, 'welcome_')) {
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['content' => $value, 'group' => 'welcome']
                );
            }
        }

        return back()->with('success', 'Template texts updated successfully! Refresh welcome page to see changes.');
    }
}

