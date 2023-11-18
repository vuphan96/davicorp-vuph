<div class="modal fade" id="modalImportOrder" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <form class="modal-content form-determine-volume row" method="post"
              action="{{ sc_route_admin('order_import.create_import_by_report') }}" style="" id="form_change_product">
            @csrf
            <input type="hidden" name="type_import" id="type_import" value="">
            <div style="overflow-y: scroll; max-height:600px; padding-right: 20px; padding: 15px 30px 15px 30px; box-sizing: border-box;">
                <div class="modal-title col-md-12" style="text-align: center">
                    <h2>Xác nhận nhập kho. Vui lòng chọn ngày NCC nhập hàng và Kho nhập.</h2>
                </div>
                <br>
                <div class="modal-content-body">
                    <div>
                        <div class="form-group row">
                            <label class="col-2">Ngày giao hàng :</label>
                            <div class="col-10">
                                <input name="import_delivery_date" id="import_delivery_date" type="text" style="width: 250px" class="date_time" placeholder="Chọn ngày">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-2">Chọn kho :</label>
                            <div class="col-10">
                                <select name="warehouse_id" id="warehouse_id" style="width: 250px">
                                    @foreach($warehouse as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-info" id="btn_submit_create_order_import">Lưu đơn</button>
                <button type="button" class="btn btn-secondary close-modal" id="close_modal_import_order">Đóng</button>
            </div>
        </form>
    </div>
</div>