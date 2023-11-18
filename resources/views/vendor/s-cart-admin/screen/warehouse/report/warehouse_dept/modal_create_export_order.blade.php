<div class="modal fade" id="exportWarehouseModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl export-modal-container" role="document" >
        <form class="modal-content form-export-warehouse" id="form-export-warehouse" method="post"
              action="javascript:void(0)"
              multiple="" style="height:700px;">
            <?php echo csrf_field(); ?>
            <div style="padding: 0 10px; box-sizing: border-box">
                <div class="modal-header row" style="padding: 1rem">
                    <div class="col-md-8 col-sm-5">
                        <h5 class="modal-title" id="exampleModalLongTitle">Tạo phiếu xuất kho</h5>
                    </div>
                </div>
            </div>
            <div class="modal-body" style="overflow-y: scroll; max-height:700px;">
                <h5 class="modal-title">Vui lòng chọn kho cần xuất: </h5>
                <div class="row">
                    <div class="col-md-4 col-sm-4">
                        <select class="form-control select2" id="select_warehouse" style="width: 90%">
                            <option value="">-- Chọn kho --</option>
                            {!! $optionWarehouse !!}
                        </select>
                    </div>
                </div>
                <br/>
                <div class="form-group row">
                    <table class="col-12 table table-hover box-body text-wrap table-bordered list_table" >
                        <thead>
                        <tr>
                            <th class="text-center">Ngày xuất kho</th>
                            <th class="text-center">Mã sản phẩm</th>
                            <th class="text-center">Tên sản phẩm</th>
                            <th class="text-center">Phiếu xuất kho</th>
                            <th class="text-center">Mã đơn hàng</th>
                            <th class="text-center">Tên khách hàng</th>
                            <th class="text-center" style="width: 100px">Số lượng nợ KH</th>
                            <th class="text-center" style="width: 130px">Số lượng trả</th>
                        </tr>
                        </thead>
                        <tbody id="data_modal_row">
                        </tbody>
                    </table>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-info" data-dismiss="modal">Thoát</button>
                <button type="button" class="btn btn-primary btn-create-export_order" onclick="handleSubmitWarehouseExport()">Xác nhận</button>
            </div>
        </form>
    </div>
</div>