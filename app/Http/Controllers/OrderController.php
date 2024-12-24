<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class OrderController extends Controller
{

  public function createOrder(Request $request)
  {
    $validated = $request->validate([
      'user_id' => 'required|exists:users,id',
    ]);

    $order = Order::create([
      'user_id' => $validated['user_id'],
    ]);

    return response()->json([
      'id' => $order->id,
      'user_id' => $order->user_id,
      'created_at' => $order->created_at->toISOString(),
    ], 201);
  }

  public function getOrders(Request $request)
  {
    $userId = $request->query('user_id');
    $page = $request->query('page', 1);

    $ordersQuery = Order::with('user:id,email,name')
      ->select(['id', 'user_id', 'created_at']);

    if ($userId) {
      $ordersQuery->where('user_id', $userId);
    }

    $orders = $ordersQuery->paginate(10, ['*'], 'page', $page);

    return response()->json([
      'page' => $orders->currentPage(),
      'orders' => $orders->items(),
    ]);
  }
}
