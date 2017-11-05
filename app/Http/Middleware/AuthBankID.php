<?php

namespace App\Http\Middleware;

use App\Services\BankID;
use Closure;

class AuthBankID
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!session()->has('status') || session('status') != BankID::COMPLETE || !session()->has('orderRef')) {

            return redirect(route('login-bankid'));
        }

        return $next($request);

    }
}
