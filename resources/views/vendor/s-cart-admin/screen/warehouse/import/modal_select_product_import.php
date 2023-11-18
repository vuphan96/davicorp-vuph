<style>
    .blue-text {
        color: blue;
    }
</style>
<div class="modal fade" id="modalSelectProductImport" tabindex="-1" role="dialog" aria-labelledby="modalSelectProductImportTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalSelectProductImportTitle">Thông tin sản phẩm</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table">
                    <tr>
                        <td><b id="productName"></b></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td id="productQty"></td>
                        <td id="productLatestImportQty"></td>
                    </tr>
                    <tr>
                        <th>Nhà cung cấp -- Chọn giá theo nhà NCC bên dưới</th>
                        <td></td>
                    <tr>
                        <td id="productPriceBoard" class="blue-text">
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>