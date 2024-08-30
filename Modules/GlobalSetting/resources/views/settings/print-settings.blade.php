@extends('admin.master_layout')
@section('title')
    <title>{{ __('Settings') }}</title>
@endsection
@section('admin-content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>{{ __('Settings') }}</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a>
                    </div>
                    <div class="breadcrumb-item">{{ __('Settings') }}</div>
                </div>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-md-12">

                        <form method="POST" action="{{ route('admin.purchase.store') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title">{{ __('Create Purchase') }}</div>
                                </div>

                                <div class="card-body">
                                    <div class="form-group row">


                                        <div class="row">
                                            <div class="col-md-12 mb-1">
                                                <h4
                                                    style="border-bottom: 1px solid #1ba161; padding-bottom: 5px; color: #1ba161">
                                                    Barcode Print
                                                </h4>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-sm-6">
                                                <label for="">Barcode Size</label>
                                                <select name="barcode_setting_id" class="form-control" required>
                                                    <option value="">Select Barcode Size</option>
                                                    <option value="1">
                                                        44mm x 34mm
                                                    </option>
                                                    <option value="2" selected>
                                                        38mm x 25mm
                                                    </option>
                                                    <option value="3">
                                                        30mm x 10mm
                                                    </option>
                                                    <option value="4">
                                                        81mm x 12mm
                                                    </option>
                                                    <option value="5">
                                                        107mm x 53mm
                                                    </option>
                                                    <option value="6">
                                                        50mm x 20mm
                                                    </option>
                                                    <option value="7">
                                                        50mm x 30mm
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-sm-6">
                                                <label for="">Printer</label>
                                                <select name="barcode_printer" class="form-control" required>
                                                    <option value="Barcode">
                                                        Barcode
                                                    </option>
                                                    <option value="A4" selected>
                                                        A4
                                                    </option>
                                                </select>
                                            </div>
                                        </div>


                                        <div class="row">
                                            <div class="col-md-12 mb-1">
                                                <h4
                                                    style="border-bottom: 1px solid #1ba161; padding-bottom: 5px; color: #1ba161">
                                                    Default Print Setting
                                                </h4>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-6">
                                                <label for="">Print</label>
                                                <select name="default_print" class="form-control" required>
                                                    <option value="">Select</option>
                                                    <option value="Pos">
                                                        Pos
                                                    </option>
                                                    <option value="A4" selected>
                                                        A4
                                                    </option>
                                                    <option value="A5">
                                                        A5
                                                    </option>
                                                </select>
                                            </div>

                                            <div class="col-md-6">
                                                <label for="">Invoice Template</label>
                                                <select name="invoice_template" class="form-control" required>
                                                    <option value="">Select Template</option>
                                                    <option value="Default" selected>
                                                        Default Amarsolution
                                                    </option>
                                                    <option value="Custom">
                                                        Custom
                                                    </option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12 mb-1">
                                                <h4
                                                    style="border-bottom: 1px solid #1ba161; padding-bottom: 5px; color: #1ba161">
                                                    Custom Invoice
                                                </h4>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="custom_invoice_header">Header size in px</label>
                                                <input type="text" name="custom_invoice_header"
                                                    id="custom_invoice_header" value="100"
                                                    class="form-control printer-machine" placeholder="100">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="custom_invoice_footer">Footer size in px</label>
                                                <input type="text" name="custom_invoice_footer"
                                                    id="custom_invoice_footer" value="100"
                                                    class="form-control printer-machine" placeholder="100">
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12 mb-1">
                                                <h4
                                                    style="border-bottom: 1px solid #1ba161; padding-bottom: 5px; color: #1ba161">
                                                    Invoice Information
                                                </h4>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-sm-6">
                                                <label for="">Business Name</label>
                                                <input type="text" name="invoice_name" value="Quick Shifter"
                                                    class="form-control" required>
                                            </div>
                                            <div class="col-sm-6">
                                                <label for="">Email</label>
                                                <input type="email" name="invoice_email" value="quickshifter21@gmail.com"
                                                    class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-sm-4">
                                                <label for="">Note</label>
                                                <textarea class="form-control" name="invoice_remark" id="elm1" cols="30" rows="10"></textarea>
                                            </div>
                                            <div class="col-sm-4">
                                                <label for="">Address</label>
                                                <textarea class="form-control" name="invoice_address" id="elm1" cols="30" rows="10"><p>Shop No - 01, Plot - 02, Road - 09, Sector -15/D, Uttara, Dhaka-1230</p></textarea>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label for="landline">Mobile</label>
                                                    <div class="input-group">
                                                        <input class="form-control" placeholder="Mobile"
                                                            name="invoice_mobile[]" value="+880 1787871041"
                                                            type="text" id="mobile" autocomplete="off">
                                                        <span class="input-group-addon" onclick='addPhone()'
                                                            style="cursor: pointer;">
                                                            <i class='fa fa-plus'></i>
                                                        </span>
                                                    </div>
                                                    <div id="multiple_phone"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-sm-6">
                                                <label for="">Reference message</label>
                                                <textarea class="form-control" name="reference_message" id="elm1" cols="30" rows="10"></textarea>
                                            </div>
                                            <div class="col-sm-6">
                                                <label for="">Terms and Conditions</label>
                                                <textarea class="form-control" name="terms_and_conditions" id="elm1" cols="30" rows="10"></textarea>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="">Invoice Logo</label>
                                                <input type="file" name="invoice_logo" class="dropify"
                                                    data-default-file="https://amarsolution.com/uploads/invoice_logo/38bnHggqDzVhL8MP9RdJEYCZ0C77xJV0v0dIYkD7.jpg" />
                                            </div>
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-check form-check-inline my-3 ml-4">
                                                            <input class="form-check-input" name="invoice_logo_print"
                                                                type="checkbox" id="invoice_logo_print" value="1"
                                                                checked>
                                                            <label class="form-check-label" for="invoice_logo_print">
                                                                Show Logo in Invoice
                                                            </label>
                                                        </div>
                                                        <div class="form-check form-check-inline my-3 ml-4">
                                                            <input class="form-check-input" name="invoice_name_print"
                                                                type="checkbox" id="invoice_name_print" value="1"
                                                                checked>
                                                            <label class="form-check-label" for="invoice_name_print">
                                                                Show Name in Invoice
                                                            </label>
                                                        </div>
                                                        <div class="form-check form-check-inline mt-3 ml-4">
                                                            <input class="form-check-input" name="show_signature_fields"
                                                                type="checkbox" id="show_signature_fields" value="1"
                                                                checked>
                                                            <label class="form-check-label" for="show_signature_fields">
                                                                Show Signature Fields
                                                            </label>
                                                        </div>
                                                        <div class="form-check form-check-inline mt-1 ml-4">
                                                            <input class="form-check-input" name="show_product_image"
                                                                type="checkbox" id="show_product_image" value="1"
                                                                checked>
                                                            <label class="form-check-label" for="show_product_image">
                                                                Show Product Image
                                                            </label>
                                                        </div>
                                                        <div class="form-check form-check-inline my-3 ml-4">
                                                            <input class="form-check-input" name="invoice_barcode_enable"
                                                                type="checkbox" id="invoice_barcode_enable"
                                                                value="1" checked>
                                                            <label class="form-check-label" for="invoice_barcode_enable">
                                                                Show Barcode in Invoice
                                                            </label>
                                                        </div>
                                                        <div class="form-check form-check-inline my-3 ml-4">
                                                            <input class="form-check-input" name="show_sku"
                                                                type="checkbox" id="show_sku" value="1">
                                                            <label class="form-check-label" for="show_sku">
                                                                Show SKU in Invoice
                                                            </label>
                                                        </div>
                                                        <div class="form-check form-check-inline my-3 ml-4">
                                                            <input class="form-check-input" name="show_shipping_address"
                                                                type="checkbox" id="show_shipping_address" value="1"
                                                                checked>
                                                            <label class="form-check-label" for="show_shipping_address">
                                                                Show Shipping address in Invoice
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 mb-1">
                                                <h4
                                                    style="border-bottom: 1px solid #1ba161; padding-bottom: 5px; color: #1ba161">
                                                    Signature
                                                </h4>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="">Signature 1</label>
                                                <input type="text" name="signature_1" value="Received By"
                                                    class="form-control" placeholder="Signature 1">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="">Signature 2</label>
                                                <input type="text" name="signature_2" value=""
                                                    class="form-control" placeholder="Signature 2">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="">Signature 3</label>
                                                <input type="text" name="signature_3" value="Authorised By"
                                                    class="form-control" placeholder="Signature 3">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 mb-1">
                                                <h4
                                                    style="border-bottom: 1px solid #1ba161; padding-bottom: 5px; color: #1ba161">
                                                    Sale Signature Image
                                                </h4>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="">Signature Image 1</label>
                                                <input type="file" name="sale_signature_1" class="dropify"
                                                    data-default-file="https://amarsolution.com/" />
                                            </div>
                                            <div class="col-md-4">
                                                <label for="">Signature Image 2</label>
                                                <input type="file" name="sale_signature_2" class="dropify"
                                                    data-default-file="https://amarsolution.com/" />
                                            </div>
                                            <div class="col-md-4">
                                                <label for="">Signature Image 3</label>
                                                <input type="file" name="sale_signature_3" class="dropify"
                                                    data-default-file="https://amarsolution.com/" />
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 mb-1">
                                                <h4
                                                    style="border-bottom: 1px solid #1ba161; padding-bottom: 5px; color: #1ba161">
                                                    Quotation Signature Image
                                                </h4>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="">Signature Image 1</label>
                                                <input type="file" name="quotation_signature_1" class="dropify"
                                                    data-default-file="https://amarsolution.com/" />
                                            </div>
                                            <div class="col-md-4">
                                                <label for="">Signature Image 2</label>
                                                <input type="file" name="quotation_signature_2" class="dropify"
                                                    data-default-file="https://amarsolution.com/" />
                                            </div>
                                            <div class="col-md-4">
                                                <label for="">Signature Image 3</label>
                                                <input type="file" name="quotation_signature_3" class="dropify"
                                                    data-default-file="https://amarsolution.com/" />
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 mb-1">
                                                <h4
                                                    style="border-bottom: 1px solid #1ba161; padding-bottom: 5px; color: #1ba161">
                                                    Silent Printing
                                                </h4>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="">Barcode Printer Name</label>
                                                <input type="text" name="barcode_printer_machine" value=""
                                                    class="form-control printer-machine"
                                                    placeholder="Barcode Printer Name">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="">Pos Printer Name</label>
                                                <input type="text" name="pos_printer_machine" value=""
                                                    class="form-control printer-machine" placeholder="Pos Printer Name">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="">A4 Printer Name</label>
                                                <input type="text" name="a4_printer_machine" value=""
                                                    class="form-control printer-machine" placeholder="A4 Printer Name">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-6">
                                                <a href="/print-server/Setup.msi" download="printServerSetup.msi">
                                                    Download Print Server
                                                </a>
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <label for="enable_silent_printing">
                                                    <input type="checkbox" id="enable_silent_printing"
                                                        name="enable_silent_printing" value="1">
                                                    Enable Silent Printing
                                                </label>
                                            </div>
                                        </div>
                                        <div class="float-right mt-3">
                                            <button type="submit" class="btn btn-success">Update</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection


@push('js')
    <script>
        prevImage('logo-upload', 'logo-preview', 'logo-label');
    </script>
@endpush
