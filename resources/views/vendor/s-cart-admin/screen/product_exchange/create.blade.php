@extends($templatePathAdmin.'layout')
<style>
    .switch {
        margin-top: 3px;
        position: relative;
        display: inline-block;
        width: 50px;
        height: 20px;
    }

    /* Hide default HTML checkbox */
    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    /* The slider */
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 17px;
        width: 17px;
        left: 4px;
        bottom: 2px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked + .slider {
        background-color: #2196F3;
    }

    input:focus + .slider {
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked + .slider:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
    }

</style>
@section('main')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header with-border">
                    <h2 class="card-title">{{ $title_description??'' }}</h2>
                    <div class="card-tools">
                        <div class="btn-group float-right mr-5">
                            <a href="{{ sc_route_admin('product_exchange.index') }}"
                               class="btn  btn-flat btn-default" title="List">
                                <i class="fa fa-list"></i><span
                                        class="hidden-xs"> {{ sc_language_render('admin.back_list') }}</span>
                            </a>
                        </div>
                    </div>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form action="{{ sc_route_admin('product_exchange.store') }}" method="post" name="form_name"
                      accept-charset="UTF-8"
                      class="form-horizontal" id="form-main" enctype="multipart/form-data">
                    <div id="main-add" class="card-body">
                        <div class="col-sm-12">
                            <label for="category" class="col-sm-12 col-form-label">
                                Sẩn phẩm cơ sở
                                <span class="required-icon" title="{{sc_language_render('note.required-field')}}">*</span>
                            </label><br>
                            <div class="input-group">
                                <select class="form-control category select2" id="product_id" data-placeholder="Chọn sản phẩm cơ sở"
                                        name="product_id" required>
                                    <option readonly value="">chọn sản phẩm cơ sở</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}">{{$product->name ?? ''}} - {{ $product->unit->name ?? '' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12" id="box-product-exchange">
                            <div class="item-product-exchange">
                                <hr style="margin-bottom: 5px !important;">
                                <label for="category" class="col-sm-12 col-form-label">
                                    Sẩn phẩm quy đổi
                                    <span class="required-icon" title="{{sc_language_render('note.required-field')}}">*</span>
                                </label><br>
                                <div class="input-group">
                                    <select onchange="selectProductExchange($(this));" class="form-control product-exchange select2"
                                            data-placeholder="chọn sản phẩm quy đổi"
                                            name="product_exchange_id[]" required>
                                        <option value="">Chọn sản phẩm quy đổi</option>
                                        @foreach ($products as $k => $productExchange)
                                            <option value="{{ $productExchange->id }}">{{$productExchange->name ?? ''}} - {{ $productExchange->unit->name ?? 'Lỗi' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <label for="category" class="col-sm-12 col-form-label">
                                    Số lượng quy đổi
                                    <span class="required-icon" title="{{sc_language_render('note.required-field')}}">*</span>
                                </label><br>
                                <div class="input-group">
                                    <input type="number" oninput="checkValueQty($(this));" class="input-qty-exchange" min="1" value="1" name="qty_exchange[]" required width="100%" placeholder="Quy đổi tương ứng">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-3" style="margin-top: 20px">
                            <div class="input-group">
                                <button
                                        type="button" class="btn btn-flat btn-success"
                                        id="btn-clone-product-exchange"
                                        title="{{sc_language_render('action.add') }}">
                                    <i class="fa fa-plus"></i> Thêm sản phẩm quy đổi
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer kind   row" id="card-footer">
                        @csrf
                        <div class="col-md-12">
                            <div class="btn-group float-left">
                                <button type="button" id="btn-submit-create-product-exchange" class="btn btn-primary">{{ sc_language_render('action.submit') }}</button>
                            </div>

                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @php
        $htmlSelectProduct =
                            '<div class="item-product-exchange">
                                <hr style="margin-bottom: 5px !important;">
                                <label for="category" class="col-sm-12 col-form-label"><span class="title-product-exchange">Sẩn phẩm quy đổi</span><span class="required-icon">*</span></label><br>
                                <div class="input-group">
                                    <select onchange="selectProductExchange($(this));" class="form-control product-exchange select2"
                                            data-placeholder="chọn sản phẩm quy đổi"
                                            name="product_exchange_id[]" required>
                                        <option value="">Chọn sản phẩm quy đổi</option>';
                                        foreach ($products as $k => $productExchange) {
                        $htmlSelectProduct .='<option value="'.$productExchange->id.'">'.$productExchange->name. ' - '. ($productExchange->unit->name ?? "Lỗi") .'</option>';
                                        }
                $htmlSelectProduct .='</select>
                                </div>
                                <label for="category" class="col-sm-12 col-form-label">Số lượng quy đổi<span class="required-icon" ">*</span></label><br>
                                <div class="input-group">
                                    <input type="number" oninput="checkValueQty($(this));" class="input-qty-exchange"  min="1" value="1" name="qty_exchange[]" required width="100%" placeholder="Quy đổi tương ứng">
                                </div>
                            </div>';
        $htmlSelectProduct = str_replace("\n", '', $htmlSelectProduct);
        $htmlSelectProduct = str_replace("\t", '', $htmlSelectProduct);
        $htmlSelectProduct = str_replace("\r", '', $htmlSelectProduct);
        $htmlSelectProduct = str_replace("'", '"', $htmlSelectProduct);
    @endphp
@endsection

@push('scripts')
    {{-- flexselect --}}
    <script src="{{ sc_file('admin/plugin/liquidmetal.js')}}" type="text/javascript"></script>
    <script src="{{ sc_file('admin/plugin/jquery.flexselect.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            //Initialize Select2 Elements
            $('.select2').select2()
        });

        $('#btn-submit-create-product-exchange').click(function () {
            let submitForm = true;
            let product_id = $('#product_id').val()
            let arrProductExchange = [];
            $('.product-exchange').each(function (index) {
                arrProductExchange.push($(this).val());
                if ($(this).val() == product_id) {
                    submitForm = false;
                    return alertMsg('error', 'Sản phẩm quy đổi trùng sản phẩm cơ sở!');
                }
            })
            let uniqueArrProductExchange = unique(arrProductExchange);
            if (uniqueArrProductExchange.length !== arrProductExchange.length) {
                alertMsg('error', 'Sản phẩm quy đổi trùng nhau!');
                submitForm = false;
            }
            $('.product-exchange').each(function (index) {
                if ($(this).val() == '' || $(this).val() === null) {
                    alertMsg('error', 'Sản phẩm quy đổi trống!');
                    submitForm = false;
                }
            })
            $('.input-qty-exchange').each(function (index) {
                if ($(this).val() == '' || $(this).val() === null) {
                    alertMsg('error', 'Số lượng quy đổi trống!');
                    submitForm = false;
                }
            })
            if (submitForm) {
                $('#form-main').submit();
            }
        })

        function alertJsCustom(type = 'error', msg = '') {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 5000
            });
            Toast.fire({
                type: type,
                title: msg
            })
        }

        // Check value có tồn tại trong ds sản phẩm
        function checkExists(inputValue) {
            var x = document.getElementById("product-list");
            var i;
            var flag;
            for (i = 0; i < x.options.length; i++) {
                if(inputValue == x.options[i].value){
                    flag = true;
                }
            }
            return flag;
        }

        function selectProductExchange(e) {
            let product_exchange_id = e.val();
            let product_id = $('#product_id').val()
            let arrProductExchange = [];

            if (product_exchange_id == product_id) {
                alertMsg('error', 'Sản phẩm quy đổi trùng sản phẩm cơ sở!');
            }
            $('.product-exchange').each(function (index) {
                arrProductExchange.push($(this).val());
            })
            let uniqueArrProductExchange = unique(arrProductExchange);
            if (uniqueArrProductExchange.length !== arrProductExchange.length) {
                return alertMsg('error', 'Sản phẩm quy đổi trùng nhau!');
            }
        }

        $('#btn-clone-product-exchange').click(function () {
            let itemProduct = '{!! $htmlSelectProduct !!}';
            $('#box-product-exchange').append(itemProduct)
            $('.select2').select2()
            $('.title-product-exchange').each(function (index) {
                $(this).text('Sản phẩm quy đổi '+ (index+1))
            })
        })

        function checkValueQty(e) {
            let value = e.val();
            if (value < 1) {
                return e.val('')
            }
        }

        /**
         * Check mảng trùng nhau
         * @param arr
         * @returns {[]}
         */
        function unique(arr) {
            let newArr = []
            for (let i = 0; i < arr.length; i++) {
                if (!newArr.includes(arr[i])) {
                    newArr.push(arr[i])
                }
            }
            return newArr
        }
    </script>
@endpush
