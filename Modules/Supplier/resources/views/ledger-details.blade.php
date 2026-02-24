@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Invoice') }}</title>
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('/backend/css/invoice.css') }}">
@endpush

@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <section class="page">

                <div class="row">
                    <div class="col-md-12">
                        <div class="d-flex justify-content-between">
                            @php
                                $person = $ledger->supplier_id ? $ledger->supplier : $ledger->customer;
                                $isSupplier = (bool) $ledger->supplier_id;
                            @endphp
                            <div class="flex-1 d-flex flex-column">
                                <span><strong>Name:</strong>&nbsp;{{ $person->name }}</span>
                                <span><strong>Mobile:</strong>&nbsp;{{ $person->phone }}</span>
                                <span><strong>Email:</strong>&nbsp;{{ $person->email }}</span>
                                <span><strong>Address:</strong>&nbsp;{{ $person->address }}</span>
                                <span><strong>{{ $isSupplier ? 'Paid By' : 'Received By' }}
                                        :</strong>&nbsp;{{ $ledger->createdBy->name }}</span>
                            </div>
                            <div class="flex-1 d-flex flex-column">
                                <span><strong>Date:</strong>&nbsp;{{ formatDate($ledger->date) }}</span>
                                <span><strong>Invoice No:</strong>&nbsp;{{ $ledger->invoice_no }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-12">
                        @if ($ledger->details->count() > 0)
                            <table class="table table-striped table-bordered mt-4" cellspacing="0" width="100%"
                                style="margin-top: 0 !important">
                                <thead class="theme-primary text-white">
                                    <tr>
                                        <th>SL</th>
                                        <th>{{ $isSupplier ? 'Purchase' : 'Sale' }} Invoice No.</th>
                                        <th class="text-right">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($ledger->details as $details)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $details->invoice }}</td>
                                            <td class="text-right">{{ currency($details->amount) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <table class="table table-striped table-bordered mt-4" cellspacing="0" width="100%"
                                style="margin-top: 0 !important">
                                <thead class="theme-primary text-white">
                                    <tr>
                                        <th>{{ __('Description') }}</th>
                                        <th class="text-right">{{ __('Amount') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ $ledger->invoice_type }}</td>
                                        <td class="text-right">{{ currency(abs($ledger->amount)) }}</td>
                                    </tr>
                                </tbody>
                                @if ($ledger->note)
                                    <tfoot>
                                        <tr>
                                            <td colspan="2"><strong>{{ __('Note:') }}</strong> {{ $ledger->note }}</td>
                                        </tr>
                                    </tfoot>
                                @endif
                            </table>
                        @endif
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
                    <a onclick="window.print()" class="btn btn-primary waves-effect waves-light">
                        <i class="fa fa-print"></i> Print
                    </a>
                    <a href="javascript:;" class="btn btn-info waves-effect waves-light">
                        <i class="fa fa-print"></i> Print POS
                    </a>
                </div>

            </section>
        </div>
    </div>
@endsection
