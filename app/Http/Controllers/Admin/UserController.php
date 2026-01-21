<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->latest();

        $q = trim((string) $request->query('q', ''));
        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                  ->orWhere('email', 'like', "%{$q}%");
            });
        }

        $role = trim((string) $request->query('role', ''));
        if ($role !== '') {
            $query->where('role', $role);
        }

        $users = $query->paginate(20)->withQueryString();
        return view('admin.users.index', compact('users', 'q', 'role'));
    }
    
    public function show(User $user)
    {
        $user->load('orders');
        return view('admin.users.show', compact('user'));
    }
}
