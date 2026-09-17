<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    /** GET /api/addresses */
    public function index(Request $request): JsonResponse
    {
        $addresses = $request->user()->addresses()
            ->orderByDesc('is_primary')
            ->orderByDesc('created_at')
            ->get();

        return response()->json(['data' => $addresses]);
    }

    /** POST /api/addresses */
    public function store(Request $request): JsonResponse
    {
        $data = $this->validated($request);

        $address = $request->user()->addresses()->create($data);
        $this->ensurePrimary($request, $address);

        return response()->json(['data' => $address->fresh()], 201);
    }

    /** PUT /api/addresses/{id} */
    public function update(Request $request, int $id): JsonResponse
    {
        $address = $request->user()->addresses()->findOrFail($id);

        $address->update($this->validated($request));
        $this->ensurePrimary($request, $address);

        return response()->json(['data' => $address->fresh()]);
    }

    /** DELETE /api/addresses/{id} */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $address = $request->user()->addresses()->findOrFail($id);
        $wasPrimary = $address->is_primary;
        $address->delete();

        if ($wasPrimary) {
            $request->user()->addresses()->first()?->update(['is_primary' => true]);
        }

        return response()->json(['message' => 'Alamat dihapus.']);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'label' => ['required', 'string', 'max:20'],
            'recipient' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'regex:/^08[0-9]{7,13}$/'],
            'address_text' => ['required', 'string', 'max:1000'],
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
            'is_primary' => ['nullable', 'boolean'],
        ]);
    }

    private function ensurePrimary(Request $request, Address $address): void
    {
        if ($address->is_primary) {
            $request->user()->addresses()
                ->where('id', '!=', $address->id)
                ->update(['is_primary' => false]);
        } elseif ($request->user()->addresses()->where('is_primary', true)->doesntExist()) {
            $address->forceFill(['is_primary' => true])->save();
        }
    }
}
