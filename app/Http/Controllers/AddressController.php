<?php

namespace App\Http\Controllers;

use App\Models\UserAddress;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    /** GET /addresses — return all addresses as JSON */
    public function index()
    {
        $addresses = auth()->user()
            ->addresses()
            ->get()
            ->map(fn($a) => $this->format($a));

        return response()->json(['addresses' => $addresses]);
    }

    /** POST /addresses — save a new address */
    public function store(Request $request)
    {
        $request->validate([
            'label'          => 'nullable|string|max:50',
            'recipient_name' => 'required|string|max:150',
            'phone'          => 'required|string|max:20',
            'address'        => 'required|string|max:300',
            'barangay'       => 'nullable|string|max:100',
            'city'           => 'nullable|string|max:100',
            'postal'         => 'nullable|string|max:20',
            'is_default'     => 'nullable|boolean',
            'lat'            => 'nullable|numeric|between:-90,90',
            'lng'            => 'nullable|numeric|between:-180,180',
        ]);

        $user      = auth()->user();
        $isDefault = $request->boolean('is_default', false);

        // If this is the first address, make it default automatically
        if ($user->addresses()->count() === 0) {
            $isDefault = true;
        }

        // If setting as default, clear existing default
        if ($isDefault) {
            $user->addresses()->update(['is_default' => false]);
        }

        $address = $user->addresses()->create([
            'label'          => $request->input('label', 'Home'),
            'recipient_name' => $request->recipient_name,
            'phone'          => $request->phone,
            'address'        => $request->address,
            'barangay'       => $request->barangay,
            'city'           => $request->city,
            'postal'         => $request->postal,
            'is_default'     => $isDefault,
            'lat'            => $request->lat,
            'lng'            => $request->lng,
        ]);

        return response()->json(['success' => true, 'address' => $this->format($address)]);
    }

    /** PUT /addresses/{address} — update an address */
    public function update(Request $request, UserAddress $address)
    {
        if ($address->user_id !== auth()->id()) abort(403);

        $request->validate([
            'label'          => 'nullable|string|max:50',
            'recipient_name' => 'required|string|max:150',
            'phone'          => 'required|string|max:20',
            'address'        => 'required|string|max:300',
            'barangay'       => 'nullable|string|max:100',
            'city'           => 'nullable|string|max:100',
            'postal'         => 'nullable|string|max:20',
            'is_default'     => 'nullable|boolean',
            'lat'            => 'nullable|numeric|between:-90,90',
            'lng'            => 'nullable|numeric|between:-180,180',
        ]);

        $user      = auth()->user();
        $isDefault = $request->boolean('is_default', $address->is_default);

        if ($isDefault) {
            $user->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
        }

        $address->update([
            'label'          => $request->input('label', $address->label),
            'recipient_name' => $request->recipient_name,
            'phone'          => $request->phone,
            'address'        => $request->address,
            'barangay'       => $request->barangay,
            'city'           => $request->city,
            'postal'         => $request->postal,
            'is_default'     => $isDefault,
            // Only overwrite coords if new ones are provided (geocode on address change)
            'lat'            => $request->has('lat') ? $request->lat : $address->lat,
            'lng'            => $request->has('lng') ? $request->lng : $address->lng,
        ]);

        return response()->json(['success' => true, 'address' => $this->format($address->fresh())]);
    }

    /** PATCH /addresses/{address}/default — set as default */
    public function setDefault(UserAddress $address)
    {
        if ($address->user_id !== auth()->id()) abort(403);

        auth()->user()->addresses()->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return response()->json(['success' => true]);
    }

    /** DELETE /addresses/{address} — delete address */
    public function destroy(UserAddress $address)
    {
        if ($address->user_id !== auth()->id()) abort(403);

        $wasDefault = $address->is_default;
        $address->delete();

        // If deleted address was default, promote the newest remaining one
        if ($wasDefault) {
            $next = auth()->user()->addresses()->latest()->first();
            if ($next) $next->update(['is_default' => true]);
        }

        return response()->json(['success' => true]);
    }

    private function format(UserAddress $a): array
    {
        return [
            'id'             => $a->id,
            'label'          => $a->label,
            'recipient_name' => $a->recipient_name,
            'phone'          => $a->phone,
            'address'        => $a->address,
            'barangay'       => $a->barangay,
            'city'           => $a->city,
            'postal'         => $a->postal,
            'is_default'     => $a->is_default,
            'full_address'   => $a->full_address,
            'lat'            => $a->lat,
            'lng'            => $a->lng,
        ];
    }
}
