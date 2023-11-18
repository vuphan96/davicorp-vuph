<div class="modal fade" id="volume-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <form class="modal-content form-determine-volume row" method="get"
              action="javascript:void(0)" style="">
            @csrf
            <input type="hidden" name="id_invoice" id="id_invoice" value="">
            <div style="overflow-y: scroll; max-height:600px; padding-right: 20px; padding: 15px 15px 15px 20px;
            box-sizing: border-box;">
                <div class="modal-title-header col-md-11">
                    <p style="font-weight: 500">Mẫu số 08a</p>
                </div>
                <div class="modal-title-header row mb-1">
                    <div class="col-md-10">
                        Mã hiệu:
                    </div>
                    <div class="col-md-2">
                        <input name="brand_code" class="form-control form-control-sm" type="text" value="">
                    </div>
                </div>
                <div class="modal-title-header row">
                    <div class="col-md-9"></div>
                    <div class="col-md-1" style="text-align: left">Số:</div>
                    <div class="col-md-2">
                        <input name="number" class="form-control form-control-sm" type="text" value="">
                    </div>
                </div>

                <div class="modal-title col-md-12" style="text-align: center">
                    <h5>BẢNG XÁC ĐỊNH GIÁ TRỊ KHỐI LƯỢNG CÔNG VIỆC HOÀN THÀNH</h5>
                </div>
                <div class="modal-content-body">
                    <div class="row mb-1">
                        <div class="col-md-4">
                            1. Đơn vị sử dụng ngân sách:
                        </div>
                        <div class="col-md-8" style="text-align: left;">
                            <input name="units_use" id="units_use" readonly class="auto_customer_name_determine form-control form-control-sm" type="text">
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-2">
                            2. Mã đơn vị:
                        </div>
                        <div class="col-md-3" style="text-align: left;">
                            <input name="units_code" class="form-control form-control-sm" type="text">
                        </div>
                        <div class="col-md-2">
                            Mã nguồn:
                        </div>
                        <div class="col-md-5" style="text-align: left">
                            <input name="source_code" class="form-control form-control-sm" type="text">
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-4">
                            3. Mã CTMTQG, Dự án ODG:
                        </div>
                        <div class="col-md-8" style="text-align: left;">
                            <input name="project_code" class="form-control form-control-sm" type="text">
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-3">
                            4. Căn cứ hợp đồng số:
                        </div>
                        <div class="col-md-3" style="text-align: left;">
                            <input name="number_contract" class="form-control form-control-sm" type="text">
                        </div>
                        <div class="col-md-2">
                            ,Ký ngày
                        </div>
                        <div class="col-md-4" style="text-align: left">
                            <input name="date_acceptance" class="form-control form-control-sm date_time rounded-0" data-date-format="dd/mm/yyyy" placeholder="Chọn ngày" type="text">
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-1">
                            Giữa
                        </div>
                        <div class="col-md-4" style="text-align: right;">
                            <input name="objecta" readonly id="objecta" class="auto_customer_department_determine department form-control form-control-sm" type="text">
                        </div>
                        <div class="col-md-1">
                            với
                        </div>
                        <div class="col-md-4" style="text-align: right;">
                            <input name="objectb" readonly id="objectb" class="auto_customer_name_determine form-control form-control-sm" type="text">
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-8">
                            Hai bên tiến hành xác định giá trị khối lượng hàng hóa như sau:
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-4">
                            5. Căn cứ biên bản nghiệm thu ngày
                        </div>
                        <div class="col-md-3" style="text-align: right;">
                            <input name="report_acceptance" id="report_acceptance" class="auto_date form-control form-control-sm date_time rounded-0" data-date-format="dd/mm/yyyy" placeholder="Chọn ngày" type="text">
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-2">
                            giữa trường
                        </div>
                        <div class="col-md-4" style="text-align: left;">
                            <input readonly name="object_start" id="object_start" class="auto_customer_name_determine form-control form-control-sm" type="text">
                        </div>
                        <div class="col-md-2">
                            với
                        </div>
                        <div class="col-md-4" style="text-align: right;">
                            <input readonly name="object_end" id="object_end" class="auto_customer_department_determine form-control form-control-sm" type="text">
                        </div>
                    </div>
                    <div>
                        <table class="table table-hover box-body text-wrap table-bordered list_table">
                            <thead>
                            <tr>
                                <th style="width: 100px">STT</th>
                                <th style="width: 200px">Tên mặt hàng</th>
                                <th style="width: 70px">Đvt</th>
                                <th style="width: 80px">Số lượng</th>
                                <th style="width: 110px">Giá bán</th>
                                <th style="width: 80px">Danh thu</th>
                            </tr>
                            </thead>
                            <tbody id="table_invoice_detail">

                            </tbody>
                        </table>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-7">
                            6. Lũy kế thanh toán khối lượng hoàn thành đến cuối kỳ trước:
                        </div>
                        <div class="col-md-4">
                            <input name="volume_finished" class="form-control form-control-sm" type="text">
                        </div>
                        <div class="col-md-1">
                            Đồng
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-4">
                            - Thanh toán tạm ứng
                        </div>
                        <div class="col-md-7">
                            <input name="pay_advance" class="form-control form-control-sm" type="text">
                        </div>
                        <div class="col-md-1">
                            Đồng
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-4">
                            7. Số dư tạm ứng đến cuối kỳ trước
                        </div>
                        <div class="col-md-7">
                            <input name="surplus" class="form-control form-control-sm" type="text">
                        </div>
                        <div class="col-md-1">
                            Đồng
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-4">
                            8. Số đề nghị thanh toán cuối kỳ
                        </div>
                        <div class="col-md-7">
                            <span class="price-payment-order"></span>
                        </div>
                        <div class="col-md-1">
                            Đồng
                        </div>
                    </div>
                    <br>
                    <div class="row mb-1">
                        <div class="col-md-4">
                            - Thanh toán tạm ứng
                        </div>
                        <div class="col-md-7">
                            <input name="pay_advance_request" class="form-control form-control-sm" type="text">
                        </div>
                        <div class="col-md-1">
                            Đồng
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-4">
                            - Thanh toán chuyển khoản
                        </div>
                        <div class="col-md-7">
                            <input name="pay_transfer" class="form-control form-control-sm" type="text">
                        </div>
                        <div class="col-md-1">
                            Đồng
                        </div>
                    </div>

                    <div class="modal-title-header row">
                        <div class="col-md-7"></div>
                        <div class="col-md-1">Ngày</div>
                        <div class="col-md-4"><input name="date" class="auto_date form-control form-control-sm date_time rounded-0" data-date-format="dd/mm/yyyy" placeholder="Chọn ngày" type="text"></div>
                    </div>
                    <br>
                    <div>
                        <div style="width: 37%; float: left; text-align: center">
                            <p>ĐẠI DIỆN BÊN A CUNG CẤP HÀNG HÓA, DICH VỤ</p>
                            <p style="margin-top: -14px">(Ký, ghi rõ họ tên và đóng dấu)</p>
                        </div>
                        <div style="width: 37%; float: right; text-align: center">
                            <p>ĐẠI DIỆN ĐƠN VỊ SỬ DỤNG NGÂN SÁCH</p>
                            <p style="margin-top: 25px">(Ký, ghi rõ họ tên và đóng dấu)</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close-modal">Đóng</button>
                <button type="button" target="_blank" class="btn btn-danger" value="export_pdf">Tải PDF</button>
                <button type="button" class="btn btn-info" value="export_excel">Tải Excel</button>
            </div>
        </form>
    </div>
</div>