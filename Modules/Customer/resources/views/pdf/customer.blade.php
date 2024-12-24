@extends('admin.layouts.pdf-layout')

@section('title', __('Customer List'))

@section('content')
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
        <thead>
            @php
                $list = [
                    __('Name'),
                    __('Phone'),
                    __('Area'),
                    __('Total Sale'),
                    __('Sale Payment'),
                    __('Sale Due'),
                    __('Advance'),
                    __('Total Due'),
                ];
            @endphp
            <tr style="background-color: #003366; color: white;">
                <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ __('SN') }}</th>
                @foreach ($list as $st)
                    <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ $st }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $index => $user)
                <tr>
                    <td>{{ ++$index }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->phone }}</td>
                    <td>{{ $user->area->name }}</td>
                    <td>{{ currency($user->sales->sum('grand_total')) }}</td>
                    <td>{{ currency($user->total_paid) }}</td>
                    <td>{{ currency($user->total_due) }}</td>
                    <td>{{ currency($user->advances()) }}</td>
                    <td>{{ currency($user->total_due - $user->total_sale_return_due) }}</td>

                    <td>
                        <div class="btn-group" role="group">
                            <button id="btnGroupDrop{{ $user->id }}" type="button"
                                class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                Action
                            </button>
                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop{{ $user->id }}">

                                <a class="dropdown-item" href="javascript:;" data-bs-toggle="modal"
                                    data-bs-target="#showCustomer{{ $user->id }}">Show</a>

                                <a class="dropdown-item" href="javascript:;" data-bs-toggle="modal"
                                    data-bs-target="#editCustomer{{ $user->id }}">Edit</a>


                                @if ($user->total_due)
                                    <a class="dropdown-item"
                                        href="{{ route('admin.customer.due-receive') }}?customer={{ $user->id }}">Due
                                        Receive</a>
                                @endif

                                <a class="dropdown-item"
                                    href="{{ route('admin.customers.due-receive.list') }}?customer={{ $user->id }}">Due
                                    Receive List</a>
                                <a class="dropdown-item"
                                    href="{{ route('admin.sales.return.list') }}?customer={{ $user->id }}">Sales
                                    Return</a>


                                <a class="dropdown-item"
                                    href="{{ route('admin.customer.due-receive') }}?customer={{ $user->id }}">Dismiss</a>


                                <a class="dropdown-item" href="javascript:;" onclick="status('{{ $user->id }}')"
                                    data-status="{{ $user->id }}">
                                    {{ $user->status == 1 ? 'Deactivated' : 'Activate' }}
                                </a>

                                <a class="dropdown-item"
                                    href="{{ route('admin.sales.index') }}?customer={{ $user->id }}">Sales</a>

                                <a class="dropdown-item"
                                    href="{{ route('admin.customers.ledger', $user->id) }}">{{ __('Ledger') }}</a>

                                <a class="dropdown-item"
                                    href="{{ route('admin.customers.advance', $user->id) }}">{{ __('Advance') }}</a>

                                <a href="javascript:;" class="dropdown-item" onclick="deleteData({{ $user->id }})">
                                    Delete</a>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach

            <tr>
                <td colspan="4" class="text-center fw-bold">
                    {{ __('Total') }}
                </td>
                <td class="fw-bold">
                    {{ currency($data['totalSale']) }}
                </td>
                <td class="fw-bold">
                    {{ currency($data['pay']) }}
                </td>
                <td class="fw-bold">
                    {{ currency($data['total_due']) }}
                </td>
                <td class="fw-bold">
                    {{ currency($data['total_advance']) }}
                </td>
                <td class="fw-bold" colspan="2">
                    {{ currency($data['total_due'] - $data['total_return_due']) }}
                </td>
            </tr>
        </tbody>
    </table>
@endsection
