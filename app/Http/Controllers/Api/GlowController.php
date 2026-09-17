<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Therapist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class GlowController extends Controller
{
    /** GET /api/glow/services — katalog layanan BossGlow */
    public function services(): JsonResponse
    {
        return response()->json(['data' => config('baikboss.glow')]);
    }

    /** GET /api/glow/therapists?gender=male|female — daftar terapis */
    public function therapists(Request $request): JsonResponse
    {
        $data = $request->validate([
            'gender' => ['nullable', Rule::in(['male', 'female'])],
        ]);

        $query = Therapist::query()->where('active', true)->orderByDesc('rating');

        if (! empty($data['gender'])) {
            $query->where('gender', $data['gender']);
        }

        return response()->json(['data' => $query->get()]);
    }

    /** POST /api/glow/bookings — simpan booking layanan glow */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'item_key' => ['required', 'string'],
            'therapist_id' => ['nullable', 'integer', 'exists:therapists,id'],
            'address_id' => ['nullable', 'integer', 'exists:addresses,id'],
            'address_text' => ['required', 'string', 'max:1000'],
            'address_lat' => ['nullable', 'numeric', 'between:-90,90'],
            'address_lng' => ['nullable', 'numeric', 'between:-180,180'],
            'schedule_date' => ['required', 'date', 'after_or_equal:today'],
            'schedule_time' => ['required', 'date_format:H:i'],
            'duration_hours' => ['nullable', 'integer', 'min:1', 'max:8'],
            'price' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $service = collect(config('baikboss.glow'))->firstWhere('key', $data['item_key']);

        if (! $service) {
            throw ValidationException::withMessages([
                'item_key' => 'Layanan tidak dikenal.',
            ]);
        }

        // Pilih terapis: yang dipilih user, atau otomatis dari pool sesuai gender
        $therapistId = $data['therapist_id'] ?? null;
        $therapistName = null;

        if ($therapistId) {
            $therapist = Therapist::where('active', true)
                ->where('gender', $service['gender'])
                ->findOrFail($therapistId);
            $therapistName = $therapist->name;
        } else {
            // "Bebas" -> system pilih terapis free sesuai gender
            $therapist = Therapist::where('active', true)
                ->where('gender', $service['gender'])
                ->inRandomOrder()
                ->first();

            if (! $therapist) {
                throw ValidationException::withMessages([
                    'therapist_id' => "Belum ada terapis {$service['gender_label']} yang tersedia. Coba lagi nanti.",
                ]);
            }

            $therapistId = $therapist->id;
            $therapistName = $therapist->name;
        }

        // Resolusi paket: default dari katalog, atau paket spesifik yang dipilih user
        // (divalidasi agar tidak bisa inject harga arbitrer)
        $duration = $service['duration_hours'];
        $price = $service['price'];
        $label = $service['label'];

        if (isset($data['duration_hours'], $data['price'])) {
            $match = collect($service['packages'] ?? [])->first(
                fn ($p) => $p['duration_hours'] === (int) $data['duration_hours'] && $p['price'] === (int) $data['price'],
            );

            if (! $match) {
                throw ValidationException::withMessages([
                    'price' => 'Paket tidak sesuai katalog layanan.',
                ]);
            }

            $duration = $match['duration_hours'];
            $price = $match['price'];
            $label = $match['label'];
        }

        $ongkir = (int) config('baikboss.ongkir_default');

        $booking = DB::transaction(function () use ($request, $data, $service, $therapistId, $therapistName, $ongkir, $label, $duration, $price) {
            return $request->user()->bookings()->create([
                'booking_code' => $this->generateCode(),
                'category' => $service['category'],
                'item_key' => $service['key'],
                'item_label' => $label,
                'duration_hours' => $duration,
                'price' => $price,
                'ongkir' => $ongkir,
                'total' => $price + $ongkir,
                'therapist_id' => $therapistId,
                'therapist_name' => $therapistName,
                'address_id' => $data['address_id'] ?? null,
                'address_text' => $data['address_text'],
                'address_lat' => $data['address_lat'] ?? null,
                'address_lng' => $data['address_lng'] ?? null,
                'schedule_date' => $data['schedule_date'],
                'schedule_time' => $data['schedule_time'],
                'notes' => $data['notes'] ?? null,
                'status' => 'pending',
            ]);
        });

        return response()->json(['data' => $this->present($booking)], 201);
    }

    /** GET /api/glow/bookings — daftar booking glow user login */
    public function index(Request $request): JsonResponse
    {
        $bookings = $request->user()->bookings()
            ->orderByDesc('created_at')
            ->get();

        return response()->json(['data' => $bookings->map(fn ($b) => $this->present($b))]);
    }

    /** GET /api/glow/bookings/{code} */
    public function show(Request $request, string $code): JsonResponse
    {
        $booking = $request->user()->bookings()
            ->where('booking_code', $code)
            ->firstOrFail();

        return response()->json(['data' => $this->present($booking)]);
    }

    private function generateCode(): string
    {
        do {
            $code = 'BG-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
        } while (Booking::where('booking_code', $code)->exists());

        return $code;
    }

    private function present(Booking $booking): array
    {
        return [
            'id' => $booking->id,
            'booking_code' => $booking->booking_code,
            'category' => $booking->category,
            'item_key' => $booking->item_key,
            'item_label' => $booking->item_label,
            'duration_hours' => $booking->duration_hours,
            'price' => $booking->price,
            'ongkir' => $booking->ongkir,
            'total' => $booking->total,
            'therapist' => [
                'name' => $booking->therapist_name,
            ],
            'address' => [
                'text' => $booking->address_text,
                'lat' => $booking->address_lat,
                'lng' => $booking->address_lng,
            ],
            'schedule' => [
                'date' => $booking->schedule_date->format('Y-m-d'),
                'time' => substr($booking->schedule_time, 0, 5),
            ],
            'notes' => $booking->notes,
            'status' => $booking->status,
            'created_at' => $booking->created_at->toIso8601String(),
        ];
    }
}
