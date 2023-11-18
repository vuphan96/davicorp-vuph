@extends($templatePathAdmin . 'layout')

@section('main')
    <div class="row">
        <div class="col-sm-12">
            @if($errors->any())
                {!! implode('', $errors->all('<div>:message</div>')) !!}
            @endif
            <div class="card">
                <!-- /.card-header -->
                <!-- form start -->
                <form action="{{ $url_action }}" method="post" accept-charset="UTF-8" class="form-horizontal"
                      id="form-main" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="is_admin" value="0">
                    <input type="hidden" name="is_weekend" value="0">
                    <div class="card-body" id="reward_container_customer">
                        <div class="row border-bottom mb-3" id="item_{{ $data->id ?? '' }}">
                            <div class="col-xs-6">
                                <div class="reward-principle">
                                    <div class="label-block">
                                        <i class="fas fa-cog"></i> Thời gian hệ thống tự động đồng bộ đơn nhà cung cấp từ báo cáo nhập hàng
                                    </div>
                                </div>
                                @foreach($data_sync as $key => $dataSync)
                                    <div class="input-block">
                                        <div class="form-group">
                                            <div class="row justify-content-between">
                                                <div class="col-md-8">
                                                    <span class="label">{{$dataSync['description']}}</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input name="time" required id="time_{{$dataSync['id']}}" value="{{$dataSync['value']}}" class="form-control time-input" type="time">
                                                </div>
                                                <div class="col-md-1">
                                                     <div>
                                                        <input class="order_check" type="checkbox" {{ $dataSync['status'] == 1 ? 'checked' : '' }}  data-toggle="toggle" data-id="{{ $dataSync['id'] }}" data-style="ios">
                                                     </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                <div class="reward-principle">
                                    <div class="label-block">
                                        <i class="fas fa-cog"></i> Chọn thời gian thông báo nhắc nhở nhà NCC và Thủ kho với đơn hàng chưa xác nhận
                                    </div>
                                </div>
                                @foreach($data_notification as $key => $dataNotification)
                                    <div class="input-block">
                                        <div class="form-group">
                                            <div class="row justify-content-between">
                                                <div class="col-md-8">
                                                    <span class="label">{{$dataNotification['description']}}</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <input name="time" required id="time_{{$dataNotification['id']}}" value="{{$dataNotification['value']}}" class="form-control time-input" type="time">
                                                </div>
                                                <div class="col-md-1">
                                                    <div>
                                                        <input class="order_check" type="checkbox" {{ $dataNotification['status'] == 1 ? 'checked' : '' }}  data-toggle="toggle" data-id="{{ $dataNotification['id'] }}" data-style="ios">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <!-- /.card-footer -->
                </form>

            </div>
        </div>
    </div>

@endsection

@push('styles')
    <style>
        .reward-principle {
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
        }

        .reward-principle .label-block {
            font-weight: 600;
            margin-bottom: 0.8rem;
        }

        .reward-principle .input-block {
            display: flex;
            flex-direction: row;
        }

        .reward-principle .input-block .form-group {
            margin-right: 16px;
            display: flex;
            flex-direction: row;
            align-items: center;
        }

        .reward-principle .input-block .form-group .label {
            margin-right: 8px;
        }
        .hidden {
            display: none;
        }
        .toggle.ios, .toggle-on.ios, .toggle-off.ios { border-radius: 20rem; }
        .toggle.ios .toggle-handle { border-radius: 20rem; }
    </style>
    <!-- Required Stylesheets -->
    <link rel="stylesheet" href="{{ sc_file('admin/plugin/bootstrap-editable.css')}}">
    <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css" rel="stylesheet">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>
    <script>
        $(".order_check").change(function () {
            let id = $(this).data('id');
            $.ajax({
                method: 'POST',
                url: '{{ route("sync_supplier.change_status") }}',
                data: {
                    'id': id,
                    _token: '{{ csrf_token() }}',
                },
                success: function (response) {
                    if (response.error == 0) {
                        alertJs('success', response.msg);
                    } else {
                        alertJs('error', response.msg);
                    }
                }
            });
        });
        $(".time-input").on("change", function() {
            let timeValue = $(this).val();
            let id = $(this).attr('id').replace('time_', '');
            sendAjaxRequest(id, timeValue);
        });
        function sendAjaxRequest(id, timeValue) {
            $.ajax({
                method: 'POST',
                url: '{{ route("sync_supplier.change_time") }}',
                data: {
                    'id': id,
                    'time': timeValue,
                    _token: '{{ csrf_token() }}',
                },
                success: function (response) {
                    if (response.error == 0) {
                        // location.reload();
                        alertJs('success', response.msg);
                    } else {
                        alertJs('error', response.msg);
                    }
                }
            });
        }
    </script>
@endpush
