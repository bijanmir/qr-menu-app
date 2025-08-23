<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Order::class);

        $query = Order::with(['restaurant', 'table', 'items'])
            ->whereHas('restaurant', function($q) {
                $q->where('tenant_id', auth()->user()->tenant_id);
            });

        // Filter by restaurant if user is manager
        if (auth()->user()->hasRole('manager')) {
            $restaurantIds = auth()->user()->restaurants()->pluck('id');
            $query->whereIn('restaurant_id', $restaurantIds);
        }

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('restaurant_id')) {
            $query->where('restaurant_id', $request->restaurant_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $orders = $query->latest()->paginate(20);

        return view('owner.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $this->authorize('view', $order);

        $order->load(['restaurant', 'table', 'items', 'payments']);

        return view('owner.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $this->authorize('update', $order);

        $validated = $request->validate([
            'status' => 'required|in:pending,accepted,preparing,ready,served,cancelled'
        ]);

        $this->orderService->updateStatus($order, $validated['status']);

        if (request()->headers->get('HX-Request')) {
            return view('partials.order-status', compact('order'));
        }

        return redirect()->back()->with('success', 'Order status updated successfully');
    }

    public function refund(Request $request, Order $order)
    {
        $this->authorize('refund', $order);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $order->total,
            'reason' => 'required|string|max:255',
            'item_ids' => 'sometimes|array',
            'item_ids.*' => 'exists:order_items,id'
        ]);

        // Calculate refund amount
        $refundAmount = $validated['amount'] ?? $this->orderService->calculateRefund(
            $order, 
            $validated['item_ids'] ?? []
        );

        // Process refund (implement with payment provider)
        // $refundResult = $paymentService->processRefund($order, $refundAmount);

        $order->update([
            'refund_amount' => $refundAmount,
            'refund_reason' => $validated['reason'],
            'refunded_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Refund processed successfully');
    }
}
