<?php

namespace App\Http\Controllers;

use App\Services\BankID;
use Illuminate\Http\Request;

class LoginBankIDController extends Controller
{

    public function index()
    {
        return view('auth.bankid.login');
    }

    /**
     * @param Request $request
     */
    public function login(Request $request, BankID $bankId)
    {

        $this->validate($request, [
            'ssn' => 'required|min:6|max:12|ssn',
        ]);

        session()->put('ssn', cleanSSN($request->ssn));

        $authResponse = $bankId->authenticate();
    }

}
