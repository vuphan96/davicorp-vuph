<div class="modal fade" id="modalHistoryReportDept" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl export-modal-container" role="document" >
        <form class="modal-content form-export-warehouse" id="form-export-warehouse" method="post"
              action="javascript:void(0)"
              multiple="" style="height:700px;">
            <div style="padding: 0 10px; box-sizing: border-box">
                <div class="modal-header row" style="padding: 1rem">
                    <div class="col-md-4 col-sm-5">
                        <h5 class="modal-title" >Lịch sử trả hàng cho khách hàng</h5>
                    </div>
                    <div class="col-md-4 col-sm-3 pb-2">
                    </div>
                    <div class="col-md-4 col-sm-4 text-right">
                    </div>
                </div>
            </div>
            <div class="modal-body" style="overflow-y: scroll; max-height:700px;">
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <span id="nameProduct" style="font-weight: 700"></span> &nbsp;-&nbsp;<span id="nameIdOrder"></span>&nbsp;-&nbsp;<span id="nameCustomer"></span>
                    </div>
                </div>
                <br/>
                <div class="form-group row">
                    <table class="col-12 table table-hover box-body text-wrap table-bordered list_table" >
                        <thead>
                        <tr>
                            <th class="text-center">
                                <label for="checkAllModal"></label><input class="selectAll-checkbox-modal grid-row-checkbox" type="checkbox" id="checkAllModal">
                            </th>
                            <th class="text-center">Ngày thao tác tạo phiếu</th>
                            <th class="text-center">Người thao tác</th>
                            <th class="text-center">Số lượng nợ</th>
                            <th class="text-center">Số lượng trả</th>
                            <th class="text-center">số lượng còn lại</th>
                        </tr>
                        </thead>
                        <tbody id="data_modal_history">
                        </tbody>
                    </table>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-info" id="btnCloseModalHistory" data-dismiss="modal">Thoát</button>
                <button type="button" class="btn btn-primary" disabled id="btnPrintStamps">In Tem</button>
            </div>
        </form>
    </div>
</div>