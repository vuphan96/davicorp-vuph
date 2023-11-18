@extends($templatePathAdmin . 'layout')

@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header with-border">
                    <h3>{{ $title_description ?? '' }}</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form action="{{ $url_action }}" method="post" accept-charset="UTF-8" class="form-horizontal"
                      id="form-main" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body" id="reward_container">
                        @foreach($data as $datum)
                            <div class="row border-bottom mb-3" id="item{{ $datum->id ?? '' }}">
                                <div class="col-xs-6">
                                    <div class="reward-principle">
                                        <div class="label-block">
                                            Thời gian đặt hàng
                                        </div>
                                        <div class="input-block">
                                            <div class="form-group">
                                                <span class="label">Từ</span>
                                                <input name="rule[{{ $datum->id ?? '' }}][from]" required
                                                       class="form-control {{ $errors->has("rule.$datum->id.from") ? 'is-invalid' : '' }}"
                                                       type="time" value="{{ $datum->from ?? '' }}">
                                            </div>
                                            <div class="form-group">
                                                <span class="label">Đến</span>
                                                <input name="rule[{{ $datum->id ?? '' }}][to]" required
                                                       class="form-control {{ $errors->has("rule.$datum->id.to") ? 'is-invalid' : '' }}"
                                                       type="time" value="{{ $datum->to ?? '' }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-6 ">
                                    <div class="reward-principle">
                                        <div class="label-block">
                                            Số điểm
                                        </div>
                                        <div class="input-block">
                                            <div class="form-group">
                                                <input name="rule[{{ $datum->id ?? '' }}][point]" required
                                                       class="form-control mr-2 {{ $errors->has("rule.$datum->id.point") ? 'is-invalid' : '' }}"
                                                       type="number" min="0"
                                                       value="{{ $datum->point ?? '' }}">
                                                <input hidden id="action{{ $datum->id ?? '' }}"
                                                       name="rule[{{ $datum->id ?? '' }}][action]">
                                                <button class="btn btn-md btn-outline-danger" type="button"
                                                        onclick="remove(this, '{{ $datum->id ?? '' }}')"><i
                                                            class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-3 pl-3 pb-3">
                        <button class="btn btn-outline-primary" onclick="add()" type="button"><i
                                    class="fas fa-plus-circle"></i>&nbsp;Thêm mới chính sách điểm
                        </button>
                    </div>
                    <div class="card-footer">
                        <div class="mt-3 float-md-right">
                            <button class="btn btn-primary"><i class="fas fa-save"></i>&nbsp;Lưu</button>
                            <a href="{{ route('admin_point_view.index') }}" class="btn btn-outline-danger"><i
                                        class="fas fa-times"></i>&nbsp;Thoát</a>
                        </div>
                    </div>

                    <!-- /.card-footer -->
                </form>

            </div>
        </div>
    </div>
    <template>
        <div class="row border-bottom mb-3" id="item">
            <div class="col-xs-6">
                <div class="reward-principle">
                    <div class="label-block">
                        Thời gian đặt hàng
                    </div>
                    <div class="input-block">
                        <div class="form-group">
                            <span class="label">Từ</span>
                            <input data-name="from" required oninvalid="this.setCustomValidity('Vui lòng nhập trường này')"
                                   oninput="this.setCustomValidity('')" name="rule[][from]" class="form-control" type="time" value="">
                        </div>
                        <div class="form-group">
                            <span class="label">Đến</span>
                            <input data-name="to" required oninvalid="this.setCustomValidity('Vui lòng nhập trường này')"
                                   oninput="this.setCustomValidity('')" name="rule[][to]" class="form-control" type="time" value="">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-6 ">
                <div class="reward-principle">
                    <div class="label-block">
                        Số điểm
                    </div>
                    <div class="input-block">
                        <div class="form-group">
                            <input data-name="point" name="rule[][point]" class="form-control mr-2" type="number"
                                   value="0" min="0">
                            <input data-name="action" hidden id="action" name="">
                            <button data-name="button" class="btn btn-md btn-outline-danger" type="button"><i
                                        class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
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

        .list {
            padding: 5px;
            margin: 5px;
            border-bottom: 1px solid #dcc1c1;
        }
    </style>
    <!-- Required Stylesheets -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/gh/hummingbird-dev/hummingbird-treeview@v3.0.4/hummingbird-treeview.min.css"
          rel="stylesheet">
@endpush

@push('scripts')
    <script>
        initCheck();
        function remove(element, id) {
            if (id.startsWith('new_')) {
                $('#item' + id).remove();
            } else {
                $('#item' + id).hide();
                $("#action" + id).val('delete');
            }
        }

        function add() {
            let template = document.getElementsByTagName("template")[0];
            let element = $(template.content.cloneNode(true));
            let container = $('#reward_container');
            let count = 'new_' + (container.children().length + 1);

            element.find('input[data-name="from"]').attr('name', 'rule[' + count + '][from]');
            element.find('#item').attr('id', 'item' + count);
            element.find('input[data-name="to"]').attr('name', 'rule[' + count + '][to]');
            element.find('input[data-name="point"]').attr('name', 'rule[' + count + '][point]');
            element.find('button[data-name="button"]').attr('onclick', 'remove(this, \'' + count + '\')');
            container.append(element);
            initCheck();
        }

        function initCheck() {
            $(document).ready(function () {
                let allInput = $('input[type=\'number\']');
                for (let i = 0; i < allInput.length; i++) {
                    let inputNum = $(allInput[i]);
                    inputNum.on('input', function () {
                        if (inputNum.val() < 0) {
                            inputNum.val(Math.abs(inputNum.val()));
                            alertMsg('error', 'Lỗi nhập dữ liệu', 'Trường dữ liệu này không cho phép nhập giá trị nhỏ hơn 0')
                        }
                    });
                }
            });
        }

    </script>
@endpush
