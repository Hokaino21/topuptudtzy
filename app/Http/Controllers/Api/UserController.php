<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * List all users (for admin use).
     */
    public function index(Request $request): JsonResponse
    {
        // TODO: Add admin check if needed
        $users = User::all();
        return response()->json(['users' => $users]);
    }
}
