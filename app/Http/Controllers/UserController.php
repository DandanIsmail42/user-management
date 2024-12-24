<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
  public function createUser(Request $request)
  {
    $validated = $request->validate([
      'email' => 'required|email|unique:users,email',
      'password' => 'required|min:8',
      'name' => 'required|string|min:3|max:50',
    ]);

    $user = User::create([
      'email' => $validated['email'],
      'password' => Hash::make($validated['password']),
      'name' => $validated['name'],
    ]);


    Mail::raw('Your account has been created successfully.', function ($message) use ($user) {
      $message->to($user->email)->subject('Account Created');
    });
    Mail::raw('A new user has registered.', function ($message) {
      $message->to('dandanismail42@gmail.com')->subject('New User Registered');
    });

    return response()->json([
      'id' => $user->id,
      'email' => $user->email,
      'name' => $user->name,
      'created_at' => $user->created_at->toISOString(),
    ], 201);
  }


  public function getUsers(Request $request)
  {
    $search = $request->query('search', null);
    $sortBy = $request->query('sortBy', 'created_at');
    $page = $request->query('page', 1);


    if (!in_array($sortBy, ['name', 'email', 'created_at'])) {
      $sortBy = 'created_at';
    }

    $query = User::where('active', true)
      ->withCount('orders');


    if ($search) {
      $query->where(function ($q) use ($search) {
        $q->where('name', 'like', "%{$search}%")
          ->orWhere('email', 'like', "%{$search}%");
      });
    }


    $users = $query->orderBy($sortBy)
      ->paginate(10, ['id', 'email', 'name', 'created_at'], 'page', $page);


    $response = [
      'page' => $users->currentPage(),
      'users' => $users->map(function ($user) {
        return [
          'id' => $user->id,
          'email' => $user->email,
          'name' => $user->name,
          'created_at' => $user->created_at->toISOString(),
          'orders_count' => $user->orders_count,
        ];
      }),
    ];

    return response()->json($response);
  }
}
