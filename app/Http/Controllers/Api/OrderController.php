<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function __construct(private OrderService $orderService)
    {
    }

    /** POST /api/quote — hitung biaya tanpa menyimpan */
    public function quote(Request $request): JsonResponse
    {
        $data = $this->validatePayload($request);
        $quote = $this->orderService->quote($data);

        return response()->json(['data' => $quote]);
    }

    /** POST /api/orders — buat pesanan + payment pending */
    public function store(Request $request): JsonResponse
    {
        $data = $this->validatePayload($request, withCustomer: true);
        $order = $this->orderService->create($data);

        return response()->json([
            'data' => $this->present($order),
        ], 201);
    }

    /** GET /api/orders/{code}?wa=628xxx — cek status pesanan */
    public function show(string $code, Request $request): JsonResponse
    {
        $order = Order::with(['items', 'payment'])
            ->where('order_code', $code)
            ->where('wa_number', $request->query('wa'))
            ->firstOrFail();

        return response()->json(['data' => $this->present($order)]);
    }

    /** POST /api/orders/{code}/pay — tandai pembayaran berhasil (simulasi konfirmasi transfer) */
    public function pay(string $code, Request $request): JsonResponse
    {
        $order = Order::with('payment')
            ->where('order_code', $code)
            ->where('wa_number', $request->input('wa'))
            ->firstOrFail();

        if ($order->status === 'cancelled') {
            return response()->json(['message' => 'Pesanan sudah dibatalkan.'], 422);
        }

        $order->payment->update([
            'status' => 'paid',
            'bank_name' => $request->input('bank_name'),
            'paid_at' => now(),
        ]);

        $order->update(['status' => 'paid']);

        return response()->json(['data' => $this->present($order->fresh(['items', 'payment']))]);
    }

    private function validatePayload(Request $request, bool $withCustomer = false): array
    {
        $rules = [
            'service_type' => ['required', Rule::in(['rumah', 'kos', 'warung', 'kantor'])],

            'origin_label' => ['required', 'string', 'max:255'],
            'origin_lat' => ['nullable', 'numeric', 'between:-90,90'],
            'origin_lng' => ['nullable', 'numeric', 'between:-180,180'],
            'origin_kecamatan_id' => ['nullable', 'integer', 'exists:tarif_kecamatan,id'],

            'dest_type' => ['required', Rule::in(['bojonegoro', 'luar'])],
            'dest_tarif_id' => ['required_if:dest_type,bojonegoro', 'nullable', 'integer', 'exists:tarif_kecamatan,id'],
            'dest_wilayah_id' => ['required_if:dest_type,luar', 'nullable', 'integer'],
            'dest_label' => ['required', 'string', 'max:255'],
            'dest_lat' => ['nullable', 'numeric', 'between:-90,90'],
            'dest_lng' => ['nullable', 'numeric', 'between:-180,180'],

            'schedule_date' => ['required', 'date', 'after_or_equal:today'],
            'schedule_time' => ['required', 'date_format:H:i'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.item_name' => ['required', 'string'],
            'items.*.qty' => ['required', 'integer', 'min:1', 'max:99'],

            'notes' => ['nullable', 'string', 'max:1000'],
        ];

        if ($withCustomer) {
            $rules['name'] = ['required', 'string', 'max:100'];
            $rules['phone'] = ['required', 'string', 'regex:/^08[0-9]{7,13}$/'];
        }

        return $request->validate($rules);
    }

    private function present(Order $order): array
    {
        return [
            'order_code' => $order->order_code,
            'name' => $order->user->name,
            'wa_number' => $order->wa_number,
            'service_type' => $order->service_type,
            'origin' => [
                'label' => $order->origin_label,
                'lat' => $order->origin_lat,
                'lng' => $order->origin_lng,
            ],
            'destination' => [
                'type' => $order->dest_type,
                'label' => $order->dest_label,
                'lat' => $order->dest_lat,
                'lng' => $order->dest_lng,
            ],
            'schedule' => [
                'date' => $order->schedule_date->format('Y-m-d'),
                'time' => substr($order->schedule_time, 0, 5),
            ],
            'items' => $order->items->map(fn ($i) => [
                'item_name' => $i->item_name,
                'qty' => $i->qty,
                'unit_price' => $i->unit_price,
                'subtotal' => $i->subtotal,
            ]),
            'amounts' => [
                'tarif' => $order->tarif_amount,
                'items' => $order->items_amount,
                'total' => $order->total_amount,
            ],
            'status' => $order->status,
            'payment' => $order->payment ? [
                'status' => $order->payment->status,
                'amount' => $order->payment->amount,
                'paid_at' => $order->payment->paid_at?->toIso8601String(),
            ] : null,
            'wa_tim' => config('baikboss.wa_tim'),
            'rekening' => config('baikboss.rekening'),
            'created_at' => $order->created_at->toIso8601String(),
        ];
    }
}
