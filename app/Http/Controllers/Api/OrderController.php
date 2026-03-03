<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Http\Resources\OrderResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    // 📌 Listar pedidos do usuário logado
    public function index(Request $request)
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->with('items.product')
            ->latest()
            ->get();

        return OrderResource::collection($orders);
    }

    // 📌 Criar pedido
    public function store(Request $request)
    {
        $data = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'address' => 'required|string',
            'payment_method' => 'required|string|in:pix,card,cash',
        ]);

        return DB::transaction(function () use ($data, $request) {

            $order = Order::create([
                'user_id' => $request->user()->id,
                'status' => 'pending',
                'total' => 0,
                'address' => $data['address'],
                'payment_method' => $data['payment_method'],
            ]);

            $total = 0;

            foreach ($data['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);

                $subtotal = $product->price * $item['quantity'];
                $total += $subtotal;

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                ]);
            }

            $order->update(['total' => $total]);

            return new OrderResource(
                $order->load('items.product')
            );
        });
    }

    // 📌 Mostrar pedido específico
    public function show(Request $request, $id)
    {
        $order = Order::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->with('items.product')
            ->firstOrFail();

        return new OrderResource($order);
    }

    // 📌 Atualizar status do pedido
    public function update(Request $request, $id)
    {
        $order = Order::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $validated = $request->validate([
            'status' => 'required|in:pending,processing,delivered,canceled',
        ]);

        $order->update(['status' => $validated['status']]);

        Log::info('Pedido atualizado', [
            'user_id' => auth()->id(),
            'order_id' => $order->id,
            'status' => $order->status,
        ]);

        return new OrderResource(
            $order->load('items.product')
        );
    }

    // 📌 Atualizar itens do pedido
    public function updateItems(Request $request, $id)
    {
        $order = Order::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $data = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        return DB::transaction(function () use ($order, $data) {

            $order->items()->delete();

            $total = 0;

            foreach ($data['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);

                $subtotal = $product->price * $item['quantity'];
                $total += $subtotal;

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                ]);
            }

            $order->update(['total' => $total]);

            Log::info('Itens do pedido atualizados', [
                'user_id' => auth()->id(),
                'order_id' => $order->id,
                'total' => $total,
            ]);

            return new OrderResource(
                $order->load('items.product')
            );
        });
    }

    // 📌 Excluir pedido
    public function destroy(Request $request, $id)
    {
        $order = Order::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $order->delete();

        Log::info('Pedido removido', [
            'user_id' => auth()->id(),
            'order_id' => $order->id,
        ]);

        return response()->json([
            'message' => 'Pedido removido com sucesso'
        ]);
    }
}