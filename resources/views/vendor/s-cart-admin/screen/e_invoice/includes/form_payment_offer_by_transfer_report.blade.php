<div class="modal fade" id="thePaymentByTransferModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <form class="modal-content form-determine-volume row" method="post"
              action="{{ sc_route_admin('admin.einvoice.payment_offer_report_print') }}" style="" id="form_payment_by_transfer" autocomplete="off">
            @csrf
            <input type="hidden" name="id_invoice" id="id_invoice_store_by_transfer" value="">
            <div style="overflow-y: scroll; max-height:600px; padding: 15px 20px 15px 30px;
            box-sizing: border-box;">
                <div id="title-payment-offer-by-transfer" class="d-none">
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
                <div id="title-payment-offer-by-transfer-vtg" class="d-none">
                    <div class=" col-md-12" style="text-align: center">
                        <h5>CỬA HÀNG THỰC PHẨM SẠCH DAVICORP VŨ TRƯỜNG GIANG</h5>
                    </div>
                    <div class="col-md-12" style="text-align: center">
                        <h5>Địa chỉ: Xóm 10, thôn 3, xã Yên Mỹ, Huyễn Thanh Trì, TP Hà Nội</h5>
                    </div>
                    <div class=" col-md-12" style="text-align: center">
                        <h5>ĐT: 04 3634 3714  -   04 3634 3961</h5>
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
                            <p id="customer_payment_by_transfer_name" style="display: inline-flex; margin: 0"> </p>
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-2">
                            Địa chỉ :
                        </div>
                        <div class="col-md-10">
                            <p id="customer_payment_by_transfer_address" style="display: inline-flex; margin: 0"> </p>
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-5">
                            Đề nghị quý khách hàng thanh toán tiền
                        </div>
                        <div class="col-md-7">
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
                            <input type="text" style="width: 90%" name="name_price_info_by_transfer" >
                        </div>
                        <div class="col-md-7">
                            <input type="number" class="input-payment-price-cash" name="number_price_info_by_transfer" >
                        </div>
                        <div class="col-md-1">
                            <span>đồng</span>
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-4">
                            <input type="text" class="input-payment-price-cash" name="name_price_by_transfer" >
                        </div>
                        <div class="col-md-7">
                            <input type="number" class="input-payment-price-cash" name="number_price_by_transfer" >
                        </div>
                        <div class="col-md-1">
                            <span>đồng</span>
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-4">
                            Tổng tiền :
                        </div>
                        <div class="col-md-7">
                            <input type="number" class="input-payment-price-cash" name="number_total_price_by_transfer" id="number_total_price_by_transfer">
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
                            <input type="text" class="input-payment-price-cash" name="text_total_price_by_transfer" id="text_total_price_by_transfer">
                        </div>
                    </div>
                    <div id="payment_transfer_store" class="d-none">
                        <div class="row mb-1">
                            <div class="col-md-7">
                                Số tiền trên được chuyển khoản vào :
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-md-2">
                                Chủ tài khoản :
                            </div>
                            <div class="col-md-7" style="font-weight: bold">
                                NGUYỄN ĐỨC ĐẠI
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-md-2">
                                Số tài khoản :
                            </div>
                            <div class="col-md-7" style="font-weight: bold">
                                6860148168888
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-md-2">
                                Tại Ngân Hàng
                            </div>
                            <div class="col-md-10" style="font-weight: bold">
                                Ngân hàng MB  Hai Bà Trưng
                            </div>
                        </div>
                    </div>
                    <div id="payment_transfer_company" class="d-none">
                        <div class="row mb-1">
                            <div class="col-md-7">
                                Số tiền trên được chuyển khoản vào :
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-md-2">
                                Chủ tài khoản :
                            </div>
                            <div class="col-md-7" style="font-weight: bold">
                                Công ty cổ phần Davicorp Việt Nam
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-md-2">
                                Số tài khoản :
                            </div>
                            <div class="col-md-7" style="font-weight: bold">
                                113002655224
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-md-2">
                                Tại Ngân Hàng
                            </div>
                            <div class="col-md-10" style="font-weight: bold">
                                Ngân hàng Vietinbank  Tràng An Hà Nội
                            </div>
                        </div>
                    </div>
                    <div id="payment_transfer_store_vtg" class="d-none">
                        <div class="row mb-1">
                            <div class="col-md-7">
                                Số tiền trên được chuyển khoản vào :
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-md-2">
                                Chủ tài khoản :
                            </div>
                            <div class="col-md-7" style="font-weight: bold">
                                Vũ Trường Giang
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-md-2">
                                Số tài khoản :
                            </div>
                            <div class="col-md-7" style="font-weight: bold">
                                03501011979598
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-md-2">
                                Tại Ngân Hàng
                            </div>
                            <div class="col-md-10" style="font-weight: bold">
                                Ngân hàng Maritime Bank Nam Hà Nội
                            </div>
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
                    <div class="row mb-1 d-none" id="sign_payment_by_transfer_store">
                        <div class="col-md-6" style="font-weight: bold">Chủ cửa hàng</div>
                        <div class="col-md-6" style="font-weight: bold">Người thành lập</div>
                    </div>
                    <div class="row mb-1 d-none" id="sign_payment_by_transfer_company">
                        <div class="col-md-6" style="font-weight: bold">Giám đốc công ty</div>
                        <div class="col-md-6" style="font-weight: bold">Người thành lập</div>
                    </div>
                    <br><br><br>
                </div>
            </div>
            <input type="hidden" name="key_print_payment_by_transfer" id="key_print_payment_by_transfer" value="">
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close_report_payment_offer close-modal" id="close_report_payment_by_transfer">Đóng</button>
                <button type="button" class="btn btn-danger" id="export_pdf_payment_by_transfer">Tải PDF</button>
                <button type="button" class="btn btn-info" id="export_excel_payment_by_transfer">Tải Excel</button>
            </div>
        </form>
    </div>
</div>
<script>
    $('#export_pdf_payment_by_transfer').click(function () {
        let ids = selectedRows().join();
        $('#id_invoice_store_by_transfer').val(ids)
        $('#key_print_payment_by_transfer').val(1);
        $('#form_payment_by_transfer').attr('target','_plank')
        $('#form_payment_by_transfer').submit()
    })
    $('#export_excel_payment_by_transfer').click(function () {
        let ids = selectedRows().join();
        $('#id_invoice_store_by_transfer').val(ids)
        $('#key_print_payment_by_transfer').val(2);
        $('#form_payment_by_transfer').attr('target','_self')
        $('#form_payment_by_transfer').submit()
    })

    $('#number_total_price_by_transfer').on('keyup', function () {
        let total_price = $(this).val();
        $('#text_total_price_by_transfer').val(DocTienBangChu(parseInt(total_price)) + ' đồng');
    })

</script>