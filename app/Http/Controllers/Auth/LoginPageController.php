<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Response;

class LoginPageController extends Controller
{
    public function __invoke(Request $request): Response
    {
        return inertia('Auth/Login');
    }
}
