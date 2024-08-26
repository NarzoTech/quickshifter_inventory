@extends('admin.master_layout')
@section('title')
    <title>{{ __('Invoice') }}</title>
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('/backend/css/invoice.css') }}">
@endpush

@section('admin-content')
    <div class="main-content">
        <div class="container-fluid">
            <section class="page">

                <div class="row">
                    <div class="col-md-12">
                        <div class="d-flex justify-content-between">
                            <div class="flex-1 d-flex flex-column">
                                <span><strong>Name:</strong>&nbsp;{{ $ledger->supplier->name }}</span>
                                <span><strong>Mobile:</strong>&nbsp;{{ $ledger->supplier->phone }}</span>
                                <span><strong>Email:</strong>&nbsp;{{ $ledger->supplier->email }}</span>
                                <span><strong>Address:</strong>&nbsp;{{ $ledger->supplier->address }}</span>
                                <span><strong>Paid By:</strong>&nbsp;{{ $ledger->createdBy->name }}</span>
                            </div>
                            <div class="flex-1 d-flex flex-column">
                                <span><strong>Date:</strong>&nbsp;{{ now()->parse($ledger->date)->format('d - M - Y') }}</span>
                                <span><strong>Invoice No:</strong>&nbsp;{{ $ledger->invoice_no }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-12">
                        <table class="table table-striped table-bordered mt-4" cellspacing="0" width="100%"
                            style="margin-top: 0 !important">
                            <thead class="theme-primary text-white">
                                <tr>
                                    <th>SL</th>
                                    <th>Purchase Invoice No.</th>
                                    <th class="text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ledger->details as $details)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $details->invoice }}
                                        </td>
                                        <td class="text-right">{{ currency($details->amount) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{-- <div class="col-md-12">
                        <p><strong>Note: </strong></p>
                    </div> --}}
                    {{-- <div class="col-md-12 bottomLine" style="display: none">
                        <div class="d-flex justify-content-between" style="margin-top: 80px">
                            <div>
                                <h5 style="border-top: 2px dotted;">PAID BY</h5>
                            </div>
                            <div>
                                <h5 style="border-top: 2px dotted;">RECEIVED BY</h5>
                            </div>
                            <div>
                                <h5 style="border-top: 2px dotted;">AUTHORISED BY</h5>
                            </div>
                        </div>
                    </div> --}}
                </div>

                <div class="print-btn d-print-none float-right">
                    <a onclick="Print()" class="btn btn-primary waves-effect waves-light">
                        <i class="fa fa-print"></i> Print
                    </a>
                    <a href="https://amarsolution.com/contact/supplier-due-pay/24108?type=posPrint"
                        class="btn btn-info waves-effect waves-light" target="_blank">
                        <i class="fa fa-print"></i> Print POS
                    </a>
                </div>

            </section>
        </div>
    </div>
@endsection
