<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TechnicalStaff;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index()
    {
        $staff = TechnicalStaff::orderBy('order', 'asc')->get();
        return view('admin.staff.index', compact('staff'));
    }

    public function create()
    {
        return view('admin.staff.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'role' => 'required|string|max:100',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'image' => 'nullable|image|max:5120',
        ]);

        $data['is_active'] = $request->has('is_active');
        $data['order'] = $data['order'] ?? (TechnicalStaff::max('order') + 1);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'staff_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('assets/images'), $filename);
            $data['image_url'] = 'assets/images/' . $filename;
        }

        TechnicalStaff::create($data);

        return redirect()->route('admin.staff.index')->with('success', 'Teknik kadro personeli eklendi.');
    }

    public function edit(TechnicalStaff $staff)
    {
        return view('admin.staff.edit', compact('staff'));
    }

    public function update(Request $request, TechnicalStaff $staff)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'role' => 'required|string|max:100',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'image' => 'nullable|image|max:5120',
        ]);

        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'staff_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('assets/images'), $filename);
            $data['image_url'] = 'assets/images/' . $filename;
        }

        $staff->update($data);

        return redirect()->route('admin.staff.index')->with('success', 'Teknik kadro personeli güncellendi.');
    }

    public function destroy(TechnicalStaff $staff)
    {
        $staff->delete();
        return redirect()->route('admin.staff.index')->with('success', 'Personel silindi.');
    }
}
