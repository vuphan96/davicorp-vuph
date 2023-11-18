<div class="modal fade" id="acceptance-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <form class="modal-content form-determine-volume row" method="post"
              action="{{ sc_route_admin('admin.einvoice.acceptance_report_print') }}" style="" id="form_acceptance_report">
            @csrf
            <input type="hidden" name="id_invoice" id="id_invoice_acceptance" value="">
            <div style="overflow-y: scroll; max-height:600px; padding-right: 20px; padding: 15px 30px 15px 30px;
            box-sizing: border-box;">
                <div class="modal-title col-md-12" style="text-align: center">
                    <h5>BIÊN BẢN NGHIỆM THU</h5>
                </div>
                <div class="modal-content-body">
                    <div class="row mb-1">
                        <div class="col-md-4">
                        </div>
                        <div class="col-md-7" style="text-align: center;">
                            <input name="einvoice_date" autocomplete="off" style="width: 200px" class="form-control form-control-sm check-null date_time rounded-0" data-date-format="dd/mm/yyyy" placeholder="Chọn ngày" type="text" required>
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-5">
                            Căn cứ hợp đồng cung cấp thực phẩm Số
                        </div>
                        <div class="col-md-3" style="text-align: left;padding-right: 0px">
                            <input name="units_use" class="form-control form-control-sm check-null" style="padding-right: 0px" type="text">
                        </div>
                        <div class="col-md-4" style="text-align: left; display: inline-flex">
                            <span>ngày &nbsp;</span>
                            <input name="units_date" autocomplete="off" style="width: 200px" class="form-control form-control-sm check-null date_time rounded-0" data-date-format="dd/mm/yyyy" placeholder="Chọn ngày" type="text" required>
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-1">giữa
                        </div>
                        <div class="col-md-5" style="text-align: left;">
                            <input name="units_code" class="form-control form-control-sm check-null" type="text">
                        </div>
                        <div class="col-md-1" style="text-align: center">Và
                        </div>
                        <div class="col-md-5" style="text-align: left">
                            <input name="source_code" class="form-control form-control-sm check-null" type="text">
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-4">
                            Theo nhu cầu thỏa thuận hai bên
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-2">
                            Hôm nay , Ngày
                        </div>
                        <div class="col-md-3" style="text-align: left;">
                            <input name="date" autocomplete="off" class="form-control form-control-sm check-null date_time rounded-0" data-date-format="dd/mm/yyyy" placeholder="Chọn ngày" type="text" value="{{ now()->format('d/m/Y') }}" required>
                        </div>
                        <div class="col-md-2">
                            chúng tôi gồm :
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-7" style="font-weight: bold">
                            1. ĐƠN VỊ SẢN XUẤT, SƠ CHẾ VÀ ĐÓNG GÓI :
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-7" style="font-weight: bold">
                            CÔNG TY CỔ PHẦN DAVICORP VIỆT NAM
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-7">
                            Địa chỉ  : Số 34B - Lô 2 – KĐT Đền Lừ 1, Hoàng Mai,  Hà Nội
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-7">
                            Điện thoại : 024.36340867
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-7">
                            Mã  số thuế : 0105426980
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-7">
                            Tài khoản : 113002655224 Tại NH VietinBank - CN Tràng An .
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-5">
                            Người đại diện    : (Bà) Nguyễn Thị Hồng Hải
                        </div>
                        <div class="col-md-2">
                        </div>
                        <div class="col-md-4">
                            Chức vụ : Giám đốc
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-7" style="font-weight: bold">
                            2. ĐƠN VỊ PHÂN PHỐI BÁN HÀNG TRỰC TIẾP:
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-7" style="font-weight: bold">
                            CỬA HÀNG THỰC PHẨM SẠCH DAVICORP
                        </div>
                    </div>

                    <div class="row mb-1">
                        <div class="col-md-10">
                            Địa chỉ  : Số 15 ngõ 40 Phố Tạ Quang Bửu, P. Bách Khoa, Q. Hai Bà Trưng, TP Hà Nội
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-7">
                            Điện thoại : 0982.229.536
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-5">
                            Người đại diện    : (Ông) Nguyễn Đức Đại
                        </div>
                        <div class="col-md-2">
                        </div>
                        <div class="col-md-4">
                            Chức vụ : Chủ cửa hàng
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-11">
                            Chủ tài khoản Nguyễn Đức Đại  Số TK : 6860.1481.68888 -  Ngân hàng MB – CN Hai Bà Trưng.
                        </div>
                    </div>

                    <div class="row mb-1">
                        <div class="col-md-4" style="font-weight: bold">
                            Bên B :
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-7" >
                            Tên khách hàng : <p id="customer_name" style="display: inline-flex; margin: 0"> </p>
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-7" >
                            Địa chỉ : <p id="customer_address" style="display: inline-flex; margin: 0"> </p>
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-7">
                            Điện thoại : <p id="customer_phone" style="display: inline-flex; margin: 0"> </p>
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-7">
                            Mã số thuế : <p id="customer_tax_code" style="display: inline-flex; margin: 0"> </p>
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-3">
                            Số tài khoản :
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="account_number_bank" value="" class="form-control form-control-sm check-null" width="100%">
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-3" style="font-weight: bold">
                            Người đại diện :
                        </div>
                        <div class="col-md-9">
                            <input type="text" name="representative_name" value="" class="form-control form-control-sm check-null" width="100%">
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-3" style="font-weight: bold">
                            Chức vụ :
                        </div>
                        <div class="col-md-9">
                            <input type="text" name="position" value="" class="form-control form-control-sm" width="100%">
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-8">
                            Hai bên thống nhất ký biên bản nghiệm thu với những nội dung sau:
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-11">
                            Nội dung công việc đã thực hiện: Bên A đã hoàn thành cung cấp thực phẩm cho bên B trong tháng
                        </div>

                    </div>
                    <div class="row mb-1">
                        <div class="col-md-2">
                            <input type="number" min="1" max="12" style="text-align: center;color: black" name="month_contract" class="form-control form-control-sm check-null" value="">
                        </div>
                        <div class="col-md-1" style="text-align: center">
                            năm
                        </div>
                        <div class="col-md-2">
                            <input type="number" min="0" style="text-align: center;color: black" name="year_contract" class="form-control form-control-sm check-null" value="">
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div style="width: 200px !important;">
                            đúng như trong hợp đồng số
                        </div>
                        <div>
                            <input style="width: 150px" type="text" name="number_contract" class="form-control form-control-sm check-null" value="">
                        </div>
                        <div style="text-align: center; width: 85px">
                            ký ngày
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="date_contract" autocomplete="off" data-date-format="dd/mm/yyyyy" placeholder="Chọn ngày" class="form-control form-control-sm check-null date_time rounded-0" value="" >
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-7">
                            theo bảng tổng hợp số lượng và giá trị hàng hóa từ ngày
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-3">
                            <input type="text" name="start_date_effective_contract" autocomplete="off" data-date-format="dd/mm/yyyy" placeholder="Chọn ngày" class="form-control form-control-sm check-null date_time rounded-0" value="" >
                        </div>
                        <div class="col-md-2" style="text-align: center">
                            đến ngày
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="end_date_effective_contract" autocomplete="off" data-date-format="dd/mm/yyyy" placeholder="Chọn ngày" class="form-control form-control-sm check-null date_time rounded-0" value="" >
                        </div>
                    </div>
                    <br>
                    <div>
                        <table class="table table-hover box-body text-wrap table-bordered list_table">
                            <thead>
                            <tr>
                                <th style="width: 125px">STT</th>
                                <th style="width: 220px">Tên mặt hàng</th>
                                <th style="width: 75px">Đvt</th>
                                <th style="width: 85px">Số lượng</th>
                                <th style="width: 120px">Giá bán</th>
                                <th style="width: 80px;">Danh thu</th>
                            </tr>
                            </thead>
                            <tbody id="table_invoice_detail_acceptance">
                            </tbody>
                        </table>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-12">
                            Bên A đã hoàn thành việc cung cấp cho bên B hàng hoá các loại theo đúng quy cách chủng loại và số lượng như trên. Bên B đồng ý nghiệm thu.
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-3">
                            Tổng giá trị nghiệm thu:
                        </div>
                        <div class="col-md-2 price-payment-order">
                        </div>
                        <div class="col-md-1">
                            Đồng
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-2" style="padding-right: 0">
                            ( Bằng chữ :
                        </div>
                        <div class="col-md-10 price-in-words">

                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-12">
                            Hai bên thống nhất nghiệm thu với nội dung nêu trên.
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-12">
                            Biên bản nghiệm thu được lập thành 02 bản có giá trị pháp lý như nhau, bên A giữ 1 bản, bên B giữ 1 bản làm căn cứ thanh toán.
                        </div>
                    </div>

                    <br>
                    <div>
                        <div style="width: 37%; float: left; text-align: center; font-weight: bold">
                            <p>ĐẠI DIỆN BÊN A</p>
                            <p style="margin-top: -14px">(Ký, ghi rõ họ tên và đóng dấu)</p>
                        </div>
                        <div style="width: 37%; float: right; text-align: center; font-weight: bold">
                            <p>ĐẠI DIỆN BÊN B</p>
                            <p style="margin-top: -14px">(Ký, ghi rõ họ tên và đóng dấu)</p>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="key_print_acceptance" id="key_print_acceptance" value="">
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close-modal" id="close_acceptance_modal">Đóng</button>
                <button type="button" class="btn btn-danger" id="export_pdf_acceptance">Tải PDF</button>
                <button type="button" class="btn btn-info" id="export_excel_acceptance">Tải Excel</button>
            </div>
        </form>
    </div>
</div>
<script>
    $('#export_pdf_acceptance').click(function () {
        let ids = selectedRows().join();
        $('#id_invoice_acceptance').val(ids)
        $('#key_print_acceptance').val(0);
        $('#form_acceptance_report').attr('target', '_plank');
        $('#form_acceptance_report').submit()
    })

    $('#export_excel_acceptance').click(function () {
        let ids = selectedRows().join();
        $('#id_invoice_acceptance').val(ids)
        $('#key_print_acceptance').val(1);
        $('#form_acceptance_report').attr('target', '_self');
        $('#form_acceptance_report').submit()
    })

    $('.check-null').keyup(function () {
        if ($(this).val() < 0) {
            return $(this).val(0);
        }
    })
</script>