<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sponsor;
use Illuminate\Http\Request;

class SponsorController extends Controller
{
    public function index()
    {
        $sponsors = Sponsor::orderBy('order', 'asc')->get();
        return view('admin.sponsors.index', compact('sponsors'));
    }

    public function create()
    {
        return view('admin.sponsors.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'category' => 'required|string|max:100',
            'website_url' => 'nullable|url|max:255',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'logo' => 'nullable|image|max:5120',
        ]);

        $data['is_active'] = $request->has('is_active');
        $data['order'] = $data['order'] ?? (Sponsor::max('order') + 1);

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = 'sponsor_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('assets/sponsors'), $filename);
            $data['logo_url'] = 'assets/sponsors/' . $filename;
        } else {
            $data['logo_url'] = 'assets/images/yalova-logo.jpg';
        }

        Sponsor::create($data);

        return redirect()->route('admin.sponsors.index')->with('success', 'Sponsor başarıyla eklendi.');
    }

    public function edit(Sponsor $sponsor)
    {
        return view('admin.sponsors.edit', compact('sponsor'));
    }

    public function update(Request $request, Sponsor $sponsor)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'category' => 'required|string|max:100',
            'website_url' => 'nullable|url|max:255',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'logo' => 'nullable|image|max:5120',
        ]);

        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = 'sponsor_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('assets/sponsors'), $filename);
            $data['logo_url'] = 'assets/sponsors/' . $filename;
        }

        $sponsor->update($data);

        return redirect()->route('admin.sponsors.index')->with('success', 'Sponsor güncellendi.');
    }

    public function destroy(Sponsor $sponsor)
    {
        $sponsor->delete();
        return redirect()->route('admin.sponsors.index')->with('success', 'Sponsor silindi.');
    }
}
