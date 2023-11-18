
<div class="modal fade" id="sendRobot" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form class="modal-content" method="post"
              action="{{ sc_route_admin('admin.einvoice.send_robot') }}" id="formSendRobot">
            @csrf
            <input type="hidden" name="ids" id="sendRobotIds" value="">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle"><i
                            class="fab fa-telegram-plane"></i> Gửi robot</h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group row">
                    <div class="col-12">
                        <div class="custom-control custom-radio custom-control-inline">
                            <input name="type_send" id="info_0" type="radio" class="custom-control-input"
                                   value="0">
                            <label for="info_0" class="custom-control-label">Tạo nháp</label>
                        </div> <br><br>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input name="type_send" id="info_1" type="radio" class="custom-control-input"
                                   value="1">
                            <label for="info_1" class="custom-control-label">Phát hành ngay</label>
                        </div> <br><br>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input name="type_send" id="info_2" type="radio" class="custom-control-input" checked
                                   value="2">
                            <label for="info_2" class="custom-control-label"></label>
                            <div style="display: flex; gap: 5px; align-items: center;">
                                <label style="margin-bottom: 0px">Tạo nháp và tự động phát hành lúc </label>
                                <select class="form-control rounded-0" name="hour_start" id="hour_start" style="width: 70px" >
                                    <option>01</option>
                                    <option>02</option>
                                    <option>03</option>
                                    <option>04</option>
                                    <option>05</option>
                                    <option>06</option>
                                    <option>07</option>
                                    <option>08</option>
                                    <option>09</option>
                                    <option>10</option>
                                    <option>11</option>
                                    <option>12</option>
                                    <option>13</option>
                                    <option>14</option>
                                    <option selected>15</option>
                                    <option>16</option>
                                    <option>17</option>
                                    <option>18</option>
                                    <option>19</option>
                                    <option>20</option>
                                    <option>21</option>
                                    <option>22</option>
                                    <option>23</option>
                                </select>
                                <label style="margin-bottom: 0px">giờ&nbsp;</label>
                                <select class="form-control rounded-0" name="minute_start" id="minute_start" style="width: 70px" >
                                    <option>00</option>
                                    <option>05</option>
                                    <option>10</option>
                                    <option>15</option>
                                    <option>20</option>
                                    <option>25</option>
                                    <option selected>30</option>
                                    <option>35</option>
                                    <option>40</option>
                                    <option>45</option>
                                    <option>50</option>
                                    <option>55</option>
                                </select>
                                <label style="margin-bottom: 0px">phút&nbsp;&nbsp;&nbsp;ngày&nbsp;</label>
                                <input class="form-control input-sm date_time" style="width: 105px" data-date-format="dd/mm/yyyy" name="date_start" id="date_start" value="<?php echo (formatDateVn(now(), false)) ?>">
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-primary" data-dismiss="modal"><i
                            class="fa fa-undo"></i> {{sc_language_render('action.discard')}}</button>
                <button type="button" id="btnSubmitSendRobot" class="btn btn-primary"><i
                            class="fab fa-telegram-plane"></i> Gửi robot</button>
            </div>
        </form>
    </div>
</div>
