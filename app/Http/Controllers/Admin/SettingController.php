<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = SiteSetting::pluck('value', 'key')->toArray();
        return view('admin.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $inputs = $request->except(['_token', 'hero_image_file', 'about_image_1_file', 'about_image_2_file']);

        // Handle file uploads if any
        if ($request->hasFile('hero_image_file')) {
            $file = $request->file('hero_image_file');
            $filename = 'hero_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('assets/images'), $filename);
            $inputs['hero_image'] = 'assets/images/' . $filename;
        }

        if ($request->hasFile('about_image_1_file')) {
            $file = $request->file('about_image_1_file');
            $filename = 'about_1_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('assets/images'), $filename);
            $inputs['about_image_1'] = 'assets/images/' . $filename;
        }

        if ($request->hasFile('about_image_2_file')) {
            $file = $request->file('about_image_2_file');
            $filename = 'about_2_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('assets/images'), $filename);
            $inputs['about_image_2'] = 'assets/images/' . $filename;
        }

        foreach ($inputs as $key => $val) {
            SiteSetting::updateOrCreate(['key' => $key], ['value' => $val]);
        }

        return redirect()->route('admin.settings.index')->with('success', 'Site ayarları ve anasayfa içerikleri başarıyla güncellendi.');
    }
}
