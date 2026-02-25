@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Supplier Advance') }}</title>
@endsection
@section('content')
    <div class="row">
        <div class="col-12 col-md-12 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="col-md-12">
                        <h4 class="section_title">
                            {{ __('Supplier Advance') }}
                        </h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <div class="well">
                                <strong class="me-2">{{ __('Name:') }}</strong>{{ $supplier->name }}<br>
                                <strong class="me-2">{{ __('Mobile:') }}</strong>{{ $supplier->phone }}<br>
                                <strong class="me-2">{{ __('Email:') }}</strong>{{ $supplier->email }}<br>
                                <strong class="me-2">{{ __('Current Advance:') }}</strong>{{ currency($supplier->advance) }}<br>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        {{-- Pay Advance Form --}}
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="section_title">{{ __('Pay Advance') }}</h4>
                                </div>
                                <div class="card-body">
                                    <form class="suppliers_adv_form"
                                        action="{{ route('admin.supplier.advance.pay', $supplier->id) }}"
                                        method="POST">
                                        @csrf
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label>{{ __('Paying Amount') }}</label>
                                                    <div class="input-group">
                                                        <div class="input-group-text">
                                                            <i class="far fa-money-bill-alt"></i>
                                                        </div>
                                                        <input class="form-control" placeholder="Paying Amount"
                                                            type="number" name="paying_amount" required
                                                            min="0.01" step="0.01" autocomplete="off">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xl-6">
                                                <div class="form-group">
                                                    <label>{{ __('Date') }}</label>
                                                    <div class="input-group">
                                                        <div class="input-group-text">
                                                            <i class="far fa-calendar-check"></i>
                                                        </div>
                                                        <input class="form-control datepicker" name="date" type="text"
                                                            value="{{ formatDate(now()) }}" autocomplete="off">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xl-6">
                                                <div class="form-group">
                                                    <label>{{ __('Paying With') }}</label>
                                                    <div class="input-group">
                                                        <div class="input-group-text">
                                                            <i class="far fa-credit-card"></i>
                                                        </div>
                                                        <select name="payment_type" class="form-control pay-payment-type">
                                                            <option value="">{{ __('Select Payment Type') }}</option>
                                                            @foreach (accountList() as $key => $list)
                                                                @if ($key != 'advance')
                                                                    <option value="{{ $key }}"
                                                                        @if ($key == 'cash') selected @endif
                                                                        data-name="{{ $list }}">{{ $list }}
                                                                    </option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="pay-account">
                                                        <input type="hidden" name="account_id" class="form-control"
                                                            value="cash" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label>{{ __('Note') }}</label>
                                                    <textarea name="note" class="form-control" placeholder="Note" rows="3"></textarea>
                                                </div>
                                            </div>
                                            <input type="hidden" name="total_amount" value="0">
                                            <div class="col-12">
                                                <button class="btn btn-primary" type="submit">{{ __('Pay Advance') }}</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- Refund Advance Form --}}
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="section_title">{{ __('Refund Advance') }}</h4>
                                </div>
                                <div class="card-body">
                                    <form class="suppliers_adv_form"
                                        action="{{ route('admin.supplier.advance.pay', $supplier->id) }}"
                                        method="POST">
                                        @csrf
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label>{{ __('Refund Amount') }}</label>
                                                    <div class="input-group">
                                                        <div class="input-group-text">
                                                            <i class="far fa-money-bill-alt"></i>
                                                        </div>
                                                        <input class="form-control" placeholder="Refund Amount"
                                                            type="number" name="refund_amount" required
                                                            min="0.01" max="{{ $supplier->advance }}"
                                                            step="0.01" autocomplete="off">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xl-6">
                                                <div class="form-group">
                                                    <label>{{ __('Date') }}</label>
                                                    <div class="input-group">
                                                        <div class="input-group-text">
                                                            <i class="far fa-calendar-check"></i>
                                                        </div>
                                                        <input class="form-control datepicker" name="date" type="text"
                                                            value="{{ formatDate(now()) }}" autocomplete="off">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xl-6">
                                                <div class="form-group">
                                                    <label>{{ __('Refund With') }}</label>
                                                    <div class="input-group">
                                                        <div class="input-group-text">
                                                            <i class="far fa-credit-card"></i>
                                                        </div>
                                                        <select name="payment_type" class="form-control refund-payment-type">
                                                            <option value="">{{ __('Select Payment Type') }}</option>
                                                            @foreach (accountList() as $key => $list)
                                                                @if ($key != 'advance')
                                                                    <option value="{{ $key }}"
                                                                        @if ($key == 'cash') selected @endif
                                                                        data-name="{{ $list }}">{{ $list }}
                                                                    </option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="refund-account">
                                                        <input type="hidden" name="account_id" class="form-control"
                                                            value="cash" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label>{{ __('Note') }}</label>
                                                    <textarea name="note" class="form-control" placeholder="Note" rows="3"></textarea>
                                                </div>
                                            </div>
                                            <input type="hidden" name="total_amount" value="0">
                                            <div class="col-12">
                                                <button class="btn btn-danger" type="submit">{{ __('Refund Advance') }}</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('js')
    <script>
        const accountsList = @json($accounts);

        function setupPaymentTypeHandler(selectSelector, accountContainerSelector) {
            $(document).on('change', selectSelector, function() {
                const accounts = accountsList.filter(account => account.account_type == $(this).val());

                if (accounts.length > 0 && $(this).val() != 'cash' && $(this).val() != 'advance') {
                    let html = '<select name="account_id" class="form-control">';
                    accounts.forEach(account => {
                        switch ($(this).val()) {
                            case 'bank':
                                html += `<option value="${account.id}">${account.bank_account_number} (${account.bank?.name})</option>`;
                                break;
                            case "mobile_banking":
                                html += `<option value="${account.id}">${account.mobile_number}(${account.mobile_bank_name})</option>`;
                                break;
                            case 'card':
                                html += `<option value="${account.id}">${account.card_number} (${account.bank?.name})</option>`;
                                break;
                        }
                    });
                    html += '</select>';
                    $(accountContainerSelector).html(html);
                } else {
                    $(accountContainerSelector).html(
                        `<input type="hidden" name="account_id" class="form-control" value="${$(this).val()}" readonly>`
                    );
                }
            });
        }

        setupPaymentTypeHandler('.pay-payment-type', '.pay-account');
        setupPaymentTypeHandler('.refund-payment-type', '.refund-account');
    </script>
@endpush
