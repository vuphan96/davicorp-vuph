<!DOCTYPE html>
<html>
<head>
    <style>
        .modal-content {
            background-color: #fff;
            color: #333;
            border-radius: 8px;
            width: 90%;
            max-width: 1200px;
            margin-left: 250px;
        }
        .modal-header {
            background-color: #007bff;
            color: #fff;
            border-radius: 8px 8px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
        }
        .modal-header .close {
            color: #fff;
            font-size: 24px;
            cursor: pointer;
        }
        .modal-body {
            padding: 10px;
            overflow-x: auto;
        }
        select.form-control {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }
        th {
            background-color: #f2f2f2;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .btn-secondary {
            background-color: #6c757d;
            color: #fff;
        }
        .btn-primary {
            background-color: #007bff;
            color: #fff;
        }
    </style>
</head>
<body>

<!-- HTML -->
<div class="modal fade" id="modalSelectWarehouse" tabindex="-1" role="dialog" aria-labelledby="modalSelectProductImportTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 90%">
        <div class="modal-content" style="width: 100%;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <select class="form-control" name="warehouse" id="warehouse" style="width: 100%">
                    <option value="">Chọn kho</option>
                    @foreach ($dataWarehouse as $warehouse)
                    <option value="{{ $warehouse->id }}">{{$warehouse->name}}</option>
                    @endforeach
                </select>
                <div style="max-height: 500px; overflow-y: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>Ngày trả hàng</th>
                                <th>Mã SP</th>
                                <th>Tên sản phẩm</th>
                                <th>Mã đơn hàng</th>
                                <th>Tên khách hàng</th>
                                <th>SL còn lại</th>
                                <th>SL nhập</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <button class="btn btn-secondary" type="button" data-dismiss="modal" aria-label="Close" onclick="closePopup()">Thoát</button>
                <button class="btn btn-primary" id="comfirmData" type="button" >Xác nhận</button>
            </div>
        </div>
    </div>
</div>


</body>
</html>
