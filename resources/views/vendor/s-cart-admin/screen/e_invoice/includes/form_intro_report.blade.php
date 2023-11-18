
<div class="modal fade intro-template-davicorp" id="form_intro_davicorp" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <form class="modal-content form-intro-davicorp row" method="get"
              action="javascript:void(0)" style="">
            @csrf
            <input type="hidden" name="name_customer" id="id_customer" value="">
            <input type="hidden" name="object_name" id="object_name" value="">
            <input type="hidden" name="object_header" class="object-header" value="">
            <div style="overflow-y: scroll; max-height:600px; padding: 20px 30px 20px 50px;
            box-sizing: border-box;">
                <div class="modal-content-body">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <p><b id="object_header"></b></p>
                        </div>
                        <div class="col-md-2"></div>
                        <div class="float-right col-md-6 text-center">
                            <span class="d-inline-block"><b>CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</b></span>
                            <span class="d-inline-block" style="border-bottom: 2px black solid"><b>Độc lập - Tự do - Hạnh phúc</b></span>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-6"></div>
                        <div class="col-md-2 text-right"><p>Hà Nội, ngày</p></div>
                        <div class="col-md-3 "><input name="invoice_date" id="invoice_date" type="text" class="form-control form-control-sm date_time rounded-0 text-center" data-date-format="dd/mm/yyyy" placeholder="Chọn ngày" value=""></div>
                    </div>
                    <div class="row">
                        <h4 class="col-md-12 text-center"><b>GIẤY GIỚI THIỆU</b></h4>
                    </div>
                    <div class="row">
                        <div class="col-md-3"><p>Kính gửi:</p></div>
                        <div class="col-md-7 customer-name"><input class="form-control form-control-sm" name="" type="text" value=""></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3"><p>Giới thiệu Ông/bà:</p></div>
                        <div class="col-md-7"><input class="form-control form-control-sm" name="name" type="text" value=""></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3"><p>Chức vụ:</p></div>
                        <div class="col-md-7"><input class="form-control form-control-sm" name="position" type="text" value=""></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3"><p>CMT số:</p></div>
                        <div class="col-md-7"><input class="form-control form-control-sm" name="cmt" type="text" value=""></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3"><p>Ngày cấp:</p></div>
                        <div class="col-md-7"><input class="form-control form-control-sm date_time rounded-0" data-date-format="dd/mm/yyyy" placeholder="Chọn ngày" name="date_supply" type="text" value=""></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3"><p>Nơi cấp:</p></div>
                        <div class="col-md-7"><input class="form-control form-control-sm" name="local_supply" type="text" value=""></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3"><p>Được cử đến:</p></div>
                        <div class="col-md-7"><input class="form-control form-control-sm" name="address" type="text" value=""></div>
                    </div>
                    <p>Về việc: </p>
                    <div>
                        <p>Đề nghị Quý cơ quan tạo điều kiện để ông (bà) có tên ở trên hoàn thành nhiệm vụ</p>
                    </div>
                    <div class="row">
                        <div class="col-md-4">Giấy này có giá trị đến hết ngày</div>
                        <div class="col-md-7"><input class="form-control form-control-sm date_time rounded-0" data-date-format="dd/mm/yyyy" placeholder="Chọn ngày" name="date_effect" type="text" value=""></div>
                    </div>
                    <b>Nơi nhận</b>
                    <div class="row">
                        <div class="col-md-2"><b>Như trên</b></div>
                        <div class="col-md-5"></div>
                        <div class="col-md-5"><b class="object-represent">GIÁM ĐỐC CÔNG TY</b></div>
                    </div>
                    <b>Lưu VT</b>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close-modal">Đóng</button>
                <button type="button" target="_blank" class="btn btn-danger" value="export_pdf_intro_davicorp">Tải PDF</button>
                <button type="button" class="btn btn-info" value="export_excel_intro_davicorp">Tải Excel</button>
            </div>
        </form>
    </div>
</div>