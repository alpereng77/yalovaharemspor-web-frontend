<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TicketPoint;
use Illuminate\Http\Request;

class TicketPointController extends Controller
{
    /**
     * Satış noktalarını listele
     */
    public function index()
    {
        $ticketPoints = TicketPoint::orderBy('order', 'asc')->get();
        return view('admin.ticket_points.index', compact('ticketPoints'));
    }

    /**
     * Yeni satış noktası ekleme formu
     */
    public function create()
    {
        return view('admin.ticket_points.create');
    }

    /**
     * Satış noktasını kaydet
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:190',
            'logo' => 'nullable|image|max:5120',
            'address' => 'nullable|string|max:500',
            'maps_url' => 'nullable|string|max:1000',
            'phone' => 'nullable|string|max:50',
            'working_hours' => 'nullable|string|max:100',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->has('is_active');
        $data['order'] = $data['order'] ?? (TicketPoint::max('order') + 1);

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = 'ticket_point_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('assets/ticket_points'), $filename);
            $data['logo_url'] = 'assets/ticket_points/' . $filename;
        } else {
            $data['logo_url'] = 'assets/images/yalova-logo.jpg';
        }

        TicketPoint::create($data);

        return redirect()->route('admin.ticket-points.index')->with('success', 'Bilet satış noktası başarıyla eklendi.');
    }

    /**
     * Düzenleme formu
     */
    public function edit(TicketPoint $ticketPoint)
    {
        return view('admin.ticket_points.edit', compact('ticketPoint'));
    }

    /**
     * Satış noktasını güncelle
     */
    public function update(Request $request, TicketPoint $ticketPoint)
    {
        $data = $request->validate([
            'name' => 'required|string|max:190',
            'logo' => 'nullable|image|max:5120',
            'address' => 'nullable|string|max:500',
            'maps_url' => 'nullable|string|max:1000',
            'phone' => 'nullable|string|max:50',
            'working_hours' => 'nullable|string|max:100',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = 'ticket_point_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('assets/ticket_points'), $filename);
            $data['logo_url'] = 'assets/ticket_points/' . $filename;
        }

        $ticketPoint->update($data);

        return redirect()->route('admin.ticket-points.index')->with('success', 'Bilet satış noktası güncellendi.');
    }

    /**
     * Satış noktasını sil
     */
    public function destroy(TicketPoint $ticketPoint)
    {
        $ticketPoint->delete();
        return redirect()->route('admin.ticket-points.index')->with('success', 'Bilet satış noktası silindi.');
    }
}
