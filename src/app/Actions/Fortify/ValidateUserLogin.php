<?php

namespace App\Actions\Fortify;

use App\Http\Requests\LoginRequest as AppLoginRequest;
use Illuminate\Http\Request;

class ValidateUserLogin
{
    public function handle(Request $request, $next)
    {
        \Log::error('ValidateUserLogin called');

        $form = app(AppLoginRequest::class);

        $request->validate($form->rules(), $form->messages());

        return $next($request);
    }
}
