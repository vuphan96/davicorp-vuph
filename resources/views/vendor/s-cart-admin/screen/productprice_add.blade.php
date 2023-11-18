@extends($templatePathAdmin . 'layout')

@section('main')
    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-8">
            <form action="{{ $url_action }}" method="post" accept-charset="UTF-8" class="form-horizontal" id="form-main">
                @csrf
                <div class="cart-body">
                    {{-- Tên bảng giá --}}
                    <div class="form-group row {{ $errors->has('name') ? ' text-red' : '' }}">
                        <label for="name"
                            class="col-sm-2 col-form-label">{{ sc_language_render('admin.tax.name') }}<span
                                style="color: red; font-size:20px">*</span></label>
                        <div class="col-sm-10 ">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                </div>
                                <input type="text" id="name-table-price" name="name" value=""
                                    class="form-control name {{ $errors->has('name') ? ' is-invalid' : '' }}">
                            </div>

                            @if ($errors->has('name'))
                                <span class="text-sm">
                                    <i class="fa fa-info-circle"></i> {{ $errors->first('name') }}
                                </span>
                            @endif

                        </div>
                    </div>
                    {{-- -- tên bảng giá --}}
                    {{-- Mã bảng giá --}}
                    <div class="form-group row {{ $errors->has('code') ? ' text-red' : '' }}">
                        <label for="code"
                            class="col-sm-2 col-form-label">{{ sc_language_render('admin.product.price.code') }}<span
                                style="color: red; font-size:20px">*</span></label>
                        <div class="col-sm-10 ">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                </div>
                                <input type="text" id="code-table-price" name="code" value=""
                                    class="form-control code {{ $errors->has('code') ? ' is-invalid' : '' }}">
                            </div>

                            @if ($errors->has('code'))
                                <span class="text-sm">
                                    <i class="fa fa-info-circle"></i> {{ $errors->first('code') }}
                                </span>
                            @else
                                <span class="form-text">
                                    {{ sc_language_render('product.sku_validate') }}
                                </span>
                            @endif

                        </div>
                    </div>
                    {{-- -- Mã bảng giá --}}
                    <div class="text-center">
                        <button type="submit" class="btn btn-warning" id="table-price-product" style="width:90px"><i
                                class="fas fa-save"></i> {{ sc_language_render('action.save') }}</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
  </div>
@endsection

@push('scripts')
    {{-- //Pjax --}}
    <script type="text/javascript">
        $('.grid-refresh').click(function() {
            $.pjax.reload({
                container: '#pjax-container'
            });
        });

        $(document).on('submit', '#button_search', function(event) {
            $.pjax.submit(event, '#pjax-container')
        })

        $(document).on('pjax:send', function() {
            $('#loading').show()
        })
        $(document).on('pjax:complete', function() {
            $('#loading').hide()
        })

        // tag a
        $(function() {
            $(document).pjax('a.page-link', '#pjax-container')
        })


        $(document).ready(function() {
            // does current browser support PJAX
            if ($.support.pjax) {
                $.pjax.defaults.timeout = 2000; // time in milliseconds
            }
        });

        @if ($buttonSort)
            $('#button_sort').click(function(event) {
                var url = '{{ $urlSort ?? '' }}?sort_shipping=' + $('#shipping_sort option:selected').val();
                $.pjax({
                    url: url,
                    container: '#pjax-container'
                })
            });
        @endif
    </script>
    {{-- //End pjax --}}
@endpush
