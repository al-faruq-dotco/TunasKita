<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
    /** Allowed payment status changes. Failed and refunded are final. */
    public const TRANSITIONS = [
        'pending'  => ['paid', 'failed'],
        'paid'     => ['refunded'],
        'failed'   => [],
        'refunded' => [],
    ];

    public function index(Request $request)
    {
        $payments = Payment::query()
            ->with(['order:id,user_id,order_code,status', 'order.user:id,name'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('method'), fn ($q) => $q->where('payment_method', $request->method))
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%' . $request->q . '%';
                $q->whereHas('order', fn ($o) => $o
                    ->where('order_code', 'like', $term)
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', $term)));
            })
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.payments.index', [
            'payments' => $payments,
            'methods'  => Payment::query()->distinct()->orderBy('payment_method')->pluck('payment_method'),
        ]);
    }

    public function show(Payment $payment)
    {
        $payment->load(['order.user', 'order.shipment']);

        return view('admin.payments.show', [
            'payment' => $payment,
            'allowed' => self::TRANSITIONS[$payment->status] ?? [],
        ]);
    }

    public function updateStatus(Request $request, Payment $payment)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['paid', 'failed', 'refunded'])],
        ]);

        $error = null;

        DB::transaction(function () use ($payment, $data, &$error) {
            $payment = Payment::with('order')->lockForUpdate()->findOrFail($payment->id);
            $new = $data['status'];

            if (! in_array($new, self::TRANSITIONS[$payment->status] ?? [], true)) {
                $error = 'Status pembayaran tidak bisa diubah dari ' . $payment->status . ' ke ' . $new . '.';
                return;
            }

            if ($new === 'paid' && $payment->order->status === 'cancelled') {
                $error = 'Pesanan ini sudah dibatalkan, pembayaran tidak bisa ditandai lunas.';
                return;
            }

            $payment->update([
                'status'  => $new,
                'paid_at' => $new === 'paid' ? now() : $payment->paid_at,
            ]);

            // A verified payment confirms an order that was still waiting.
            if ($new === 'paid' && $payment->order->status === 'pending') {
                $payment->order->update(['status' => 'confirmed']);
            }
        });

        if ($error) {
            return back()->with('error', $error);
        }

        return back()->with('success', 'Status pembayaran diperbarui.');
    }
}