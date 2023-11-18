<div class="modal fade" id="changeProductItemModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <form class="modal-content form-determine-volume row" method="post"
              action="{{ sc_route_admin('order_import.store_change_product') }}" style="" id="form_change_product">
            @csrf
            <input type="hidden" name="detail_id" id="detail_id" value="">
            <div style="overflow-y: scroll; max-height:600px; padding-right: 20px; padding: 15px 30px 15px 30px;
            box-sizing: border-box;">
                <div class="modal-title col-md-12" style="text-align: center">
                    <h2>Thông tin nhà cung cấp sản phẩm</h2>
                </div>
                <div class="modal-title col-md-12" style="text-align: left">
                    <h5>Tên sản phẩm : <span id="text_name_product"></span></h5>
                    <h5>Giá hiện tại : <span id="text_price"></span></h5>
                    <h5>Nhà cung cấp hiện tại : <span id="text_name_supplier"></span></h5>
                </div>
                <div class="modal-content-body">
                    <div>
                        <table class="table table-hover box-body text-wrap table-bordered list_table">
                            <thead>
                            <tr>
                                <th style="width: 50px !important; max-width: 50px !important;"></th>
                                <th style="max-width: 400px">Tên nhà cung cấp</th>
                                <th style="width: 200px;max-width: 200px">Giá nhập sản phẩm</th>
                            </tr>
                            </thead>
                            <tbody id="detail_product_supplier">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <input type="hidden" name="key_print_acceptance" id="key_print_acceptance" value="">
            <div class="modal-footer">
                <button type="submit" class="btn btn-info" id="btn_change_supplier">Đổi nhà cung cấp</button>
                <button type="button" class="btn btn-secondary close-modal" id="close_modal_change_product">Đóng</button>
            </div>
        </form>
    </div>
</div>