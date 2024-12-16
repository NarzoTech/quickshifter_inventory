    <script src="{{ asset('backend/assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('backend/assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('backend/assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('backend/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('backend/assets/vendor/js/menu.js') }}"></script>
    <script src="{{ asset('backend/clockpicker/dist/bootstrap-clockpicker.js') }}"></script>
    <script src="{{ asset('global/js/all.min.js') }}"></script>



    <!-- Vendors JS -->
    <script src="{{ asset('backend/assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>

    <!-- Main JS -->
    <script src="{{ asset('backend/assets/js/main.js') }}"></script>


    <!-- Page JS -->
    <script src="{{ asset('backend/assets/js/dashboards-analytics.js') }}"></script>
    <script src="{{ asset('backend/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('backend/js/select2.min.js') }}"></script>
    <script src="{{ asset('backend/js/tagify.js') }}"></script>
    <script src="{{ asset('backend/js/jquery.nice-select.min.js') }}"></script>
    <script src="{{ asset('backend/js/sticky_sidebar.js') }}"></script>
    <script src="{{ asset('backend/clockpicker/dist/bootstrap-clockpicker.js') }}"></script>
    <script src="{{ asset('backend/tinymce/js/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ asset('global/toastr/toastr.min.js') }}"></script>
    <script src="{{ asset('backend/js/custom.js') }}"></script>

    <script>
        @session('messege')
        var type = "{{ Session::get('alert-type', 'info') }}"
        switch (type) {
            case 'info':
                toastr.info("{{ $value }}", '', options);
                break;
            case 'success':
                toastr.success("{{ $value }}", '', options);
                break;
            case 'warning':
                toastr.warning("{{ $value }}", '', options);
                break;
            case 'error':
                toastr.error("{{ $value }}", '', options);
                break;
        }
        @endsession
    </script>


    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            }
        });
    </script>

    <script>
        function numberOnly(str) {
            let val = str.replace(/[^0-9.]/g, '');

            return parseFloat(val)
        }

        function handleError(error) {
            console.log(error);
            if (error.responseJSON) {
                if (error.responseJSON.message) {
                    toastr.error(error.responseJSON.message, '', options);
                }
                if (error.responseJSON.errors) {
                    $.each(error.responseJSON.errors, function(index, data) {
                        toastr.error(data, '', options);
                    })
                }

            }
        }

        function convertToSlug(text) {
            return text
                .toLowerCase()
                .replace(/ /g, '-')
                .replace(/[^\w-]+/g, '');
        }
    </script>

    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <script>
                toastr.error('{{ $error }}');
            </script>
        @endforeach
    @endif


    {{-- account type function --}}


    <script>
        function accountsType(accounts, html, val) {
            accounts.forEach(account => {
                switch (val) {
                    case 'bank':
                        html +=
                            `<option value="${account.id}">${account.bank_account_number} (${account.bank?.name})</option>`;
                        break;
                    case "mobile_banking":
                        html +=
                            `<option value="${account.id}">${account.mobile_number}(${account.mobile_bank_name})</option>`;
                        break;
                    case 'card':
                        html +=
                            `<option value="${account.id}">${account.card_number} (${account.bank?.name})</option>`;
                        break;
                    default:
                        break;
                }
            });
            html += '</select>';
            return html;
        }
    </script>


    <script>
        function handleStatus(route) {
            $.ajax({
                url: route,
                type: 'post',
                success: function(res) {
                    toastr.success(res.message);
                },
                error: function(err) {
                    handleError(err)
                }
            })
        }

        function prevImage(inputId, previewId, labelId) {
            $.uploadPreview({
                input_field: "#" + inputId,
                preview_box: "#" + previewId,
                label_field: "#" + labelId,
                label_default: "{{ __('Choose Image') }}",
                label_selected: "{{ __('Change Image') }}",
                no_label: false,
                success_callback: null
            });
        }

        $(document).ready(function() {
            'use strict';
            $('.export').on('click', function() {
                // get full url including query string
                var fullUrl = window.location.href;
                if (fullUrl.includes('?')) {
                    fullUrl += '&export=true';
                } else {
                    fullUrl += '?export=true';
                }

                window.location.href = fullUrl;
            })
            $('.form-reset').on('click', function() {
                // get full url without query string
                var fullUrl = window.location.href;
                if (fullUrl.includes('?')) {
                    fullUrl = fullUrl.split('?')[0];
                }

                window.location.href = fullUrl;
            })
        })
    </script>


    @stack('js')
