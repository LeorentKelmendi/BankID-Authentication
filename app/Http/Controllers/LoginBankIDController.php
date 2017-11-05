<?php

namespace App\Http\Controllers;

use App\Services\BankID;
use Exception;
use Illuminate\Http\Request;

class LoginBankIDController extends Controller
{

    public function __construct()
    {

        $this->middleware('bankid.redirect-if-auth', ['only' => 'index']);
    }
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

        if ($authResponse == BankID::ALREADY_IN_PROGRESS) {

            return view('auth.bankid.login-wait');
        }

        session()->put([

            'orderRef'       => $authResponse['orderRef'],
            'autoStartToken' => $authResponse['autoStartToken'],

        ]);

        return view('auth.bankid.login-wait');
    }

    /**
     * @param BankID $bankId
     * @return mixed
     */
    public function checkLogin(BankID $bankId, Request $request)
    {

        if (!$request->wantsJson()) {
            throw new Exception('Incorrect request!');
        }

        if (!session()->has('orderRef')) {

            throw new Exception('Mising Parameters!');
        }

        try {

            $status = $bankId->collectStatus(session('orderRef'));

            if ($status == BankID::COMPLETE) {

                session()->put(['status' => BankID::COMPLETE]);

                $url = route('profile');

            } elseif ($status == BankID::INVALID_PARAMETERS) {

                $url = route('login-bankid');

            } else {

                throw new Exception($bankId->getMessage($status));
            }

            $response = [

                'success'  => true,
                'redirect' => $url,
            ];

        } catch (Exception $e) {

            $response = [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }

        return response()->json($response);
    }

    public function logout()
    {

        session()->flush();

        return redirect(route('login-bankid'));
    }

    public function home()
    {

        dd("Welcome on home page");
    }

}
