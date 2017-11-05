<?php

namespace App\Http\Controllers;

class ProfileController extends Controller
{
    public function __construct()
    {

        $this->middleware('bankid.auth');

    }

    public function index()
    {

        dd("profile");
    }
}
