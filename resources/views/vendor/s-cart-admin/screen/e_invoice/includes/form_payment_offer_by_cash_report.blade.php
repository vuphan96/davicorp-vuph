<div class="modal fade" id="thePaymentByCashModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <form class="modal-content form-determine-volume row" method="post"
              action="{{ sc_route_admin('admin.einvoice.payment_offer_report_print') }}" style="" id="form_payment_by_cash" autocomplete="off">
            @csrf
            <input type="hidden" name="id_invoice" id="id_invoice_store_by_cash" value="">
            <div style="overflow-y: scroll; max-height:600px; padding: 15px 20px 15px 30px;
            box-sizing: border-box;">
                <div id="title-payment-offer-by-cash" class="d-none">
                    <div class=" col-md-12" style="text-align: center">
                        <h5>CÔNG TY CỔ PHẦN DAVICORP VIỆT NAM</h5>
                    </div>
                    <div class="col-md-12" style="text-align: center">
                        <h5>Địa chỉ: Số 34b Lô 2, Đền lừ 1, Hoàng Mai, Hà Nội</h5>
                    </div>
                    <div class=" col-md-12" style="text-align: center">
                        <h5>ĐT: 04 3634 3714 - 04 3634 3961</h5>
                    </div>
                    <div class=" col-md-12" style="text-align: center">
                        <h5>Website: Davicorp.vn - Email : davicorp.vn@gmail.com</h5>
                    </div>
                </div>
                <br>
                <div class="modal-title col-md-12" style="text-align: center">
                    <h5>GIẤY ĐỀ NGHỊ THANH TOÁN</h5>
                </div>
                <div class="modal-content-body">
                    <div class="row mb-1">
                        <div class="col-md-2">
                            Kính gửi :
                        </div>
                        <div class="col-md-7">
                            <p id="customer_payment_by_cash_name" style="display: inline-flex; margin: 0"> </p>
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-2">
                            Địa chỉ :
                        </div>
                        <div class="col-md-10">
                            <p id="customer_payment_by_cash_address" style="display: inline-flex; margin: 0"> </p>
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-2">
                            Tôi tên là  :
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="staff_name">
                        </div>
                        <div class="col-md-2">
                            CMT số  :
                        </div>
                        <div class="col-md-4">
                            <input type="tel" name="staff_no_id">
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-2">
                            Ngày cấp  :
                        </div>
                        <div class="col-md-4">
                            <input style="width: 187px" autocomplete="off" name="identification_date" class="form-control form-control-sm date_time rounded-0" data-date-format="dd/mm/yyyy" placeholder="Chọn ngày" type="text">
                        </div>
                        <div class="col-md-2">
                            Nơi cấp :
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="identification_place">
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-2">
                            Địa chỉ  :
                        </div>
                        <div class="col-md-7" id="location_payment">

                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-5">
                            Đề nghị quý khách hàng thanh toán tiền
                        </div>
                        <div class="col-md-6">
                            <input type="text" name="infor_payment" style="width: 300px">
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-12">
                            cho đơn vị chúng tôi như sau :
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-4" style="display: inline-flex">
                            <input type="text" style="width: 96.5%" name="name_price_one_by_cash" >
                        </div>
                        <div class="col-md-7">
                            <input type="number" class="input-payment-price-cash" name="number_price_one_by_cash" >
                        </div>
                        <div class="col-md-1">
                            <span>đồng</span>
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-4" style="display: inline-flex">
                            <input type="text" style="width: 96.5%" name="name_price_two_by_cash" >
                        </div>
                        <div class="col-md-7">
                            <input type="number" class="input-payment-price-cash" name="number_price_two_by_cash" >
                        </div>
                        <div class="col-md-1">
                            <span>đồng</span>
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-4" style="display: inline-flex">
                            <input type="text" style="width: 96.5%" name="name_price_three_by_cash" >
                        </div>
                        <div class="col-md-7">
                            <input type="number" class="input-payment-price-cash" name="number_price_three_by_cash" >
                        </div>
                        <div class="col-md-1">
                            <span>đồng</span>
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-4" style="display: inline-flex">
                            <input type="text" style="width: 96.5%" name="name_price_four_by_cash" >
                        </div>
                        <div class="col-md-7">
                            <input type="number" class="input-payment-price-cash" name="number_price_four_by_cash" >
                        </div>
                        <div class="col-md-1">
                            <span>đồng</span>
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-4">
                            <input type="text" style="width: 96.5%" name="name_price_five_by_cash" >
                        </div>
                        <div class="col-md-7">
                            <input type="number" class="input-payment-price-cash" name="number_price_five_by_cash" >
                        </div>
                        <div class="col-md-1">
                            <span>đồng</span>
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-4">
                            <input type="text" style="width: 96.5%" name="name_price_six_by_cash" >
                        </div>
                        <div class="col-md-7">
                            <input type="number" class="input-payment-price-cash" name="number_price_six_by_cash">
                        </div>
                        <div class="col-md-1">
                            <span>đồng</span>
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-4">
                            Tổng tiền
                            <input type="text" style="margin-left: 3px; width: 66%" name="name_total_price_info_by_cash" >
                        </div>
                        <div class="col-md-7">
                            <input type="number" class="input-payment-price-cash" name="number_total_price_by_cash" id="number_total_price_by_cash">
                        </div>
                        <div class="col-md-1">
                            <span>đồng</span>
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-4">
                            Bằng chữ :
                        </div>
                        <div class="col-md-7">
                            <input type="text" class="input-payment-price-cash" name="text_total_price_by_cash" id="text_total_price_by_cash">
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-11">
                            Trân trọng cảm ơn!
                        </div>
                    </div>
                    <div class="modal-title-header row">
                        <div class="col-md-6"></div>
                        <div class="col-md-2">Hà Nội, ngày</div>
                        <div class="col-md-4">
                            <input name="date_create" autocomplete="off" class="form-control form-control-sm date_time rounded-0" data-date-format="dd/mm/yyyy" placeholder="Chọn ngày" value="{{ now()->format('d/m/Y') }}" type="text">
                        </div>
                    </div>
                    <br>
                    <div class="row mb-1">
                        <div class="col-md-3 d-none" id="sign_payment_cash_store" style="font-weight: bold">Chủ cửa hàng</div>
                        <div class="col-md-3 d-none" id="sign_payment_cash_company" style="font-weight: bold">Giám đốc</div>
                        <div class="col-md-3" style="font-weight: bold">Kế toán</div>
                        <div class="col-md-3" style="font-weight: bold">Người thanh toán</div>
                        <div class="col-md-3" style="font-weight: bold">Người nhận tiền</div>
                    </div>
                    <br><br><br>
                </div>
            </div>
            <input type="hidden" name="key_print_payment_by_cash" id="key_print_payment_by_cash" value="">
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close_report_payment_offer close-modal" id="close_report_payment_by_cash">Đóng</button>
                <button type="button" class="btn btn-danger" id="export_pdf_payment_by_cash">Tải PDF</button>
                <button type="button" class="btn btn-info" id="export_excel_payment_by_cash">Tải Excel</button>
            </div>
        </form>
    </div>
</div>
<script>
    $('#export_pdf_payment_by_cash').click(function () {
        let ids = selectedRows().join();
        $('#id_invoice_store_by_cash').val(ids)
        $('#key_print_payment_by_cash').val(1);
        $('#form_payment_by_cash').attr('target','_plank')
        $('#form_payment_by_cash').submit()
    })
    $('#export_excel_payment_by_cash').click(function () {
        let ids = selectedRows().join();
        $('#id_invoice_store_by_cash').val(ids)
        $('#key_print_payment_by_cash').val(2);
        $('#form_payment_by_cash').attr('target', '_self');
        $('#form_payment_by_cash').submit()
    })

    $('#number_total_price_by_cash').on('keyup', function () {
        let total_price = $(this).val();
        $('#text_total_price_by_cash').val(DocTienBangChu(parseInt(total_price)) + ' đồng');
    })

</script>