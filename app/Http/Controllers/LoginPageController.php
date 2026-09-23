<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Response;

class LoginPageController extends Controller
{
    public function __invoke(Request $request): Response
    {
        return inertia('Auth/Login');
    }
}
