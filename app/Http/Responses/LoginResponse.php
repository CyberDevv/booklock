<?php

namespace App\Http\Responses;

use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Symfony\Component\HttpFoundation\Response;

class LoginResponse implements LoginResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Response
     */
    public function toResponse($request): Response
    {
        $user = Auth::user();

        if ($user->hasRole('Admin')) {
            return redirect()->route('dashboard');
        }

        return redirect()->route('home');
    }
}
