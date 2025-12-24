<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ __('Other Sales Report') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
        }

        .invoice-box {
            max-width: 1200px;
            margin: auto;
            padding: 30px;
            font-size: 14px;
            line-height: 24px;
            color: #555;
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
            border-collapse: collapse;
        }

        .invoice-box table td {
            padding: 8px;
            vertical-align: top;
        }

        .invoice-box table tr td:nth-child(2) {
            text-align: left;
        }

        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }

        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }

        table {
            border: 1px solid #ddd;
        }

        table thead tr th {
            background-color: #f8f9fa;
            color: #000;
            border: 1px solid #ddd;
            font-size: 12px;
        }

        table tbody tr td {
            border: 1px solid #ddd;
            font-size: 12px;
        }

        .text-center {
            text-align: center;
        }

        .font-weight-bold {
            font-weight: bold;
        }
    </style>
</head>

<body>
    @php
        $setting = cache('setting');
    @endphp
    <div class="invoice-box">
        <table>
            <tr class="top">
                <td colspan="11">
                    <table>
                        <tr>
                            <td class="title text-center" colspan="11">
                                <h2>{{ $setting->app_name }}</h2>
                                <h3>{{ __('Other Sales Report (Sales From Outside)') }}</h3>
                                <span style="font-size: 14px; font-weight: normal">{{ __('Date') }}:
                                    {{ date('d M, Y') }}</span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <table>
            <thead>
                <tr>
                    <th class="text-center">{{ __('Sl') }}</th>
                    <th class="text-center">{{ __('Date') }}</th>
                    <th class="text-center">{{ __('Invoice') }}</th>
                    <th class="text-center">{{ __('Customer') }}</th>
                    <th class="text-center">{{ __('Product Name') }}</th>
                    <th class="text-center">{{ __('Quantity') }}</th>
                    <th class="text-center">{{ __('Purchase Price') }}</th>
                    <th class="text-center">{{ __('Selling Price') }}</th>
                    <th class="text-center">{{ __('Profit') }}</th>
                    <th class="text-center">{{ __('Remark') }}</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $rowNumber = 0;
                @endphp
                @foreach ($sales as $key => $sale)
                    @php
                        // Get only products from outside (source = 2)
                        $outsideProducts = $sale->details->where('source', 2);
                    @endphp
                    @foreach ($outsideProducts as $pIndex => $detail)
                        <tr>
                            @if($pIndex == 0)
                                <td class="text-center" rowspan="{{ $outsideProducts->count() }}">{{ ++$rowNumber }}</td>
                                <td class="text-center" rowspan="{{ $outsideProducts->count() }}">{{ formatDate($sale->order_date) }}</td>
                                <td class="text-center" rowspan="{{ $outsideProducts->count() }}">{{ $sale->invoice }}</td>
                                <td class="text-center" rowspan="{{ $outsideProducts->count() }}">{{ $sale?->customer?->name ?? 'Guest' }}</td>
                            @endif
                            <td class="text-center">
                                @if($detail->product_id)
                                    {{ $detail->product->name ?? 'N/A' }}
                                @elseif($detail->service_id)
                                    {{ $detail->service->name ?? 'N/A' }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td class="text-center">{{ $detail->quantity }}</td>
                            <td class="text-center">{{ currency($detail->purchase_price) }}</td>
                            <td class="text-center">{{ currency($detail->selling_price) }}</td>
                            <td class="text-center">{{ currency(($detail->selling_price - $detail->purchase_price) * $detail->quantity) }}</td>
                            @if($pIndex == 0)
                                <td class="text-center" rowspan="{{ $outsideProducts->count() }}">{{ $sale->sale_note }}</td>
                            @endif
                        </tr>
                    @endforeach
                @endforeach
                <tr style="background-color: #f8f9fa;">
                    <td colspan="5" class="text-center font-weight-bold">{{ __('Total') }}</td>
                    <td class="text-center font-weight-bold">{{ $data['total_quantity'] }}</td>
                    <td class="text-center font-weight-bold">{{ currency($data['total_purchase']) }}</td>
                    <td class="text-center font-weight-bold">{{ currency($data['total_selling']) }}</td>
                    <td class="text-center font-weight-bold">{{ currency($data['total_profit']) }}</td>
                    <td class="text-center"></td>
                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>
