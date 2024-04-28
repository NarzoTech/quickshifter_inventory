<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>Refund Request Module</title>
</head>

<body>
    <div class="container">
        <h1 class="text-center">Refund Request Module</h1>

        <div class="card text-left">
            <div class="card-body">
                <h4 class="card-title">Refund form</h4>

                <form action="{{ route('refund-request.store') }}" method="POST">
                    @csrf


                    <div class="form-group">
                        <label for="">Order Id <small>(Only paid order will be visible)</small></label>
                        <select name="order_id" id="" class="form-control">
                            <option value="">Select</option>
                            @foreach ($order_list as $order)
                                <option value="{{ $order->id }}">{{ $order->order_id }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="">Reasone</label>
                        <textarea name="reasone" class="form-control" id="" cols="30" rows="3"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="">Bank info for refun amount</label>
                        <textarea name="account_information" class="form-control" id="" cols="30" rows="3"></textarea>
                    </div>


                    <button class="btn btn-primary">{{ __('Submit') }}</button>


                </form>
            </div>
        </div>


        <table class="table table-striped">
            <thead>
                <th>{{ __('SN') }}</th>
                <th>{{ __('Order Id') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Action') }}</th>
            </thead>

            @foreach ($refund_requests as $index => $refund_request)
                <tr>
                    <td>{{ ++$index }}</td>

                    <td>#<a target="_blank" href="">{{ $refund_request?->order?->order_id }}</a> </td>

                    <td>
                        @if ($refund_request->status == 'success')
                            {{ __('Success') }}
                        @elseif ($refund_request->status == 'rejected')
                            {{ __('Rejected') }}
                        @else
                            {{ __('Pending') }}
                        @endif
                    </td>

                    <td>
                        <a href="{{ route('refund-request.show', $refund_request->id) }}"
                            class="btn btn-primary btn-sm">Details</a>

                    </td>
                </tr>
            @endforeach


        </table>

    </div>

    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>


</body>

</html>
