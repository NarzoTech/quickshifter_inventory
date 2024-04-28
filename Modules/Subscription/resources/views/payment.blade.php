<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Subscription Plan Module</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="{{ asset('global/toastr/toastr.min.css') }}">

  </head>

  <body>

    <div class="container text-center">
        <h1 class="my-5">Subscription Plan Module</h1>


        <div class="row">
            <div class="col-md-8">
                @include('subscription::payment_basic')

                @include('subscription::payment_addon')

            </div>
            <div class="col-md-4">
                <div class="card" style="width: 18rem;">
                    <div class="card-header">
                      {{ $plan->plan_name }}
                    </div>
                    <ul class="list-group list-group-flush">
                      <li class="list-group-item">Price : {{ currency($plan->plan_price) }}</li>
                      <li class="list-group-item">Expiration : {{ $plan->expiration_date }}</li>
                    </ul>
                  </div>
            </div>
        </div>




















    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>

    <script src="{{ asset('global/toastr/toastr.min.js') }}"></script>


    <script>
        @if(Session::has('messege'))
        var type="{{Session::get('alert-type','info')}}"
        switch(type){
            case 'info':
                toastr.info("{{ Session::get('messege') }}");
                break;
            case 'success':
                toastr.success("{{ Session::get('messege') }}");
                break;
            case 'warning':
                toastr.warning("{{ Session::get('messege') }}");
                break;
            case 'error':
                toastr.error("{{ Session::get('messege') }}");
                break;
        }
        @endif
    </script>

    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <script>
                toastr.error('{{ $error }}');
            </script>
        @endforeach
    @endif

    @stack('payment-script')

  </body>
</html>
