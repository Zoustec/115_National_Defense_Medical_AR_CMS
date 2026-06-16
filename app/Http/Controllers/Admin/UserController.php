<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Contracts\UserServiceInterface;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(protected UserServiceInterface $userService) {}

    public function index(Request $request): View
    {
        $tab = $request->query('tab') === 'teacher' ? 'teacher' : 'student';
        $filters = array_merge($request->all(), ['tab' => $tab]);
        $users = $this->userService->list($filters);

        return view('admin.users.index', compact('users', 'tab'));
    }

    public function show(User $user): View
    {
        return view('admin.users.show', compact('user'));
    }
}
