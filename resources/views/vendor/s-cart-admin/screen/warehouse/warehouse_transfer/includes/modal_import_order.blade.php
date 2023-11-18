<div class="modal fade" id="modalChooseImportOrder" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <form class="modal-content form-determine-volume row" method="post" style="" id="form_choose_import_order">
            @csrf
            <div style="overflow-y: scroll; max-height:600px; padding-right: 20px; padding: 15px 30px 15px 30px;
            box-sizing: border-box;">
                <div class="modal-title col-md-12" style="text-align: center">
                    <h2>Danh sách các đơn hàng nhập theo tìm kiếm</h2>
                </div>
                <div class="modal-content-body">
                    <div>
                        <table class="table table-hover box-body text-wrap table-bordered list_table">
                            <thead>
                            <tr>
                                <th style="width: 50px !important; max-width: 50px !important;"></th>
                                <th style="max-width: 150px">Mã đơn nhập</th>
                                <th style="width: 400px;max-width: 400px">Tên nhà cung cấp</th>
                                <th style="width: 100px;max-width: 200px">Tổng tiền</th>
                            </tr>
                            </thead>
                            <tbody id="detail_product_order_import">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-info" id="btn_choose_import_order">Chọn đơn nhập</button>
                <button type="button" class="btn btn-secondary close-modal" id="close_modal_choose_import_order">Đóng</button>
            </div>
        </form>
    </div>
</div>