<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function view(User $user, Order $order)
    {
        return $order->user_id === $user->id;
    }

    public function update(User $user, Order $order)
    {
         return $order->user_id === $user->id
        && $order->status === 'pending';
    }

    public function delete(User $user, Order $order)
    {
        return $order->user_id === $user->id;
    }
}