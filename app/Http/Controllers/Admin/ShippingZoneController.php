<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingZone;
use Illuminate\Http\Request;

class ShippingZoneController extends Controller
{
    public function index()
    {
        $zones = ShippingZone::orderBy('area_name')->get();
        return view('admin.shipping-zones', compact('zones'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'area_name' => 'required|string|max:255',
            'cost'      => 'required|numeric|min:0',
        ]);

        ShippingZone::create(['area_name' => $request->area_name, 'cost' => $request->cost]);
        return back()->with('success', 'Zona pengiriman ditambahkan.');
    }

    public function update(Request $request, ShippingZone $zone)
    {
        $request->validate([
            'area_name'    => 'required|string|max:255',
            'cost'         => 'required|numeric|min:0',
            'is_available' => 'nullable|boolean',
        ]);

        $zone->update([
            'area_name'    => $request->area_name,
            'cost'         => $request->cost,
            'is_available' => $request->has('is_available'),
        ]);

        return back()->with('success', 'Zona pengiriman diperbarui.');
    }

    public function destroy(ShippingZone $zone)
    {
        $zone->delete();
        return back()->with('success', 'Zona pengiriman dihapus.');
    }
}
