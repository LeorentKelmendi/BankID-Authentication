@extends('layouts.app')

@section('content')

    <div class="container-fluid page-container">
        <div class="login-bankid-page row">

            <div class="container">
                <div class="row">

                    <div class="col-md-4 col-md-offset-4 login-bankid-container">
                        <div class="bankid-log-container"><img src="{{asset('images/bankid.png')}}" width="300px;" height="300px;"></div>
                        {{ Form::open(['url' => route('try-login-bankd'), 'method' => 'POST', 'class' =>'form-vertical']) }}

                        @if (isset($errors) && $errors->any())

                            <div class="alert alert-danger">
                                <button class="close" data-close="alert"></button>
                                <span> {{ $errors->first() }}</span>
                            </div>
                        @endif

                        <div class="form-group">
                            <div class="form-group">
                                <label for="ssn" class="sr-only control-label">Personnummer</label>
                                {{ Form::text('ssn', old('ssn'), ['class' => 'form-control', 'placeholder' => 'ÅÅMMDD-NNNN', 'autocomplete' => 'off', 'required', 'autofocus']) }}

                            </div>
                        </div>

                        <input type="hidden" value="trancezonale" name="password">


                        <button type="submit" class="btn btn-default btn-login-bankid text-center">Logga in</button>


                        {{ Form::close() }}
                    </div>

                </div>
            </div>

        </div>
    </div>
@endsection
