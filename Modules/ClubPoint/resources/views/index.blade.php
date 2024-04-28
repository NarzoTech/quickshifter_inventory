<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>Club Point Module</title>
</head>

<body>
    <div class="container">
        <h1 class="text-center">Club Point Module</h1>



        <h1>Total Club Point : {{ $histories->where('status', 'pending')->sum('club_point') }}</h1>

        <h1>Wallet Balance : {{ currency($wallet_balance) }}</h1>

        <h3>Club Point To Wallet conversion Rate : 1 USD = {{ $setting->club_point_rate }}</h3>
        <table class="table table-striped">
            <thead>
                <th>{{ __('SN') }}</th>
                <th>{{ __('Order Id') }}</th>
                <th>{{ __('Club Point') }}</th>
                <th>{{ __('Earned At') }}</th>
                <th>{{ __('Status') }}</th>
            </thead>

            @forelse ($histories as $index => $history)
                <tr>
                    <td>{{ ++$index }}</td>


                    <td>#{{ $history?->order?->order_id }}</td>
                    <td>{{ $history->club_point }}</td>
                    <td>{{ $history->created_at->format('H:iA, d M Y') }}</td>
                    <td>

                        @if ($history->status == 'pending')
                            @if ($history?->order?->order_status == 'success')
                                <a href="{{ route('clubpoint-convert', $history->id) }}">Convert Now</a>
                            @else
                                Refund
                            @endif
                        @else
                            Done
                        @endif
                    </td>
                </tr>
            @empty
                <x-empty-table :name="__('')" route="" create="no" :message="__('No data found!')"
                    colspan="5"></x-empty-table>
            @endforelse


        </table>



    </div>

    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>


</body>

</html>
