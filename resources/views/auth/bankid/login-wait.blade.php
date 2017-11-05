@extends('layouts.app')

@section('content')

    <div class="container-fluid page-container">
        <div class="login-bankid-page row">

            <div class="container">
                <div class="row">

                    <div class="col-md-4 col-md-offset-4  ">
                        <div class="bankid-log-container"><img src="{{asset('images/bankid.png')}}" width="300px;" height="300px;"></div>
                        <div id="bankid-loading">
                            <div id="loading">
                                <img src="{{asset('/images/load.gif')}}" class="spinner text-center" style=" margin-left: 110px;" width="50" height="50">
                            </div>
                            <p id="message"></p>
                            <a href="#" target="">
                                <button type="submit" class="btn btn-primary">Open Mobile BankID</button>
                                <button type="submit" class="btn btn-default ">Cancel</button>

                            </a>
                        </div>

                    </div>

                </div>
            </div>

        </div>
    </div>
    <style>
        #bankid-loading {
            border: 1px solid #BBB;
            margin: 20px 0;
            padding: 20px;
            text-align: center;
            background: whitesmoke;
            border-radius: 10px;
            box-shadow: inset 0px 0px 20px rgba(0, 0, 0, 0.1);
        }

        #bankid-message {
            margin: 20px 0 5px;
        }


    </style>
@endsection
@section('bottom')

<script src="/js/jquery.min.js"></script>
<script>

    checkStatus();

    function checkStatus(){
    $.getJSON('{{route('check-status-json')}}', function(response){

        if(response.success){

            window.location.href=response.redirect;
            return false;
        }

        $('#message').text(response.message);
        setTimeout(function(){
            checkStatus();
        }, 500);
    });


    }

</script>


@endsection
