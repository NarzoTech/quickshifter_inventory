@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Edit Supplier Due Payment') }}</title>
@endsection

@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-md-8 offset-md-2">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="section_title">{{ __('Edit Supplier Due Payment') }}</h4>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <span class="fw-bold">{{ __('Supplier') }}:</span>
                                    {{ $payment->supplier->name ?? '-' }}
                                </div>
                                <div class="mb-3">
                                    <span class="fw-bold">{{ __('Purchase Invoice') }}:</span>
                                    {{ $payment->purchase?->invoice_number ?? '-' }}
                                </div>

                                <form action="{{ route('admin.supplier.due-pay.update', $payment->id) }}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <label>{{ __('Amount') }} <span class="text-danger">*</span></label>
                                        <input type="number" name="amount" class="form-control" step="0.01" min="0.01"
                                            value="{{ old('amount', $payment->amount) }}" required>
                                        @error('amount')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>{{ __('Payment Date') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="payment_date" class="form-control datepicker"
                                            value="{{ old('payment_date', formatDate($payment->payment_date)) }}" required autocomplete="off">
                                        @error('payment_date')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>{{ __('Account') }}</label>
                                        <select name="account_id" class="form-control">
                                            @foreach ($accounts as $account)
                                                <option value="{{ $account->id }}" @selected($payment->account_id == $account->id)>
                                                    {{ $account->account_type }} {{ $account->bank ? '- ' . $account->bank->name : '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>{{ __('Note') }}</label>
                                        <textarea name="note" class="form-control" rows="3">{{ old('note', $payment->note) }}</textarea>
                                    </div>
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('admin.suppliers.due-pay-history') }}" class="btn btn-danger">{{ __('Cancel') }}</a>
                                        <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
