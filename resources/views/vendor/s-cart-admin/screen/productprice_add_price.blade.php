@extends($templatePathAdmin.'layout')

@section('main')
<div class="row">
  <div class="col-md-1"></div>
  <div class="col-md-8">
      <div class="cart-body">
        <div class="form-group row ">
          <label for="name" class="col-sm-2 col-form-label">{{ sc_language_render('admin.tax.name') }}</label>
          <div class="col-sm-10 ">
            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
              </div>
              <input type="text" id="name-table-price" value="{{$objProPrice->name}}" class="form-control name {{ $errors->has('name') ? ' is-invalid' : '' }}" disabled>
            </div>
          </div>
        </div>
      </div>
  </div>
</div>
<div class="row">
  <div class="col-md-1"></div>
  <div class="col-md-6">
    <h3 style="font-weight:600; font-size:24px">{{ sc_language_render('admin.productprice.price') }}</h3>
  </div>
</div>
<div class="row">

  <div class="col-md-6">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">{!! $title_action !!}</h3>
       
      </div>
      <!-- /.card-header -->
      <!-- form start -->
      <form action="{{Route('admin_price.add',['id'=>$objProPrice->id])}}" method="post" accept-charset="UTF-8" class="form-horizontal" >
        @csrf
       
          <input type="hidden" name="name" id="id_product_price" value="{{$objProPrice->id}}">
        <div class="card-body">

          <div class="form-group row {{ $errors->has('idProduct') ? ' text-red' : '' }}">
            <label for="idProduct" class="col-sm-3 col-form-label">{!! sc_language_render('admin.productprice.selectproduct') !!}</label>
            <div class="col-sm-9 ">
              <div class="input-group mb-3">
                <select class="form-control input-sm" style="width: 100%;" name="idProduct">
                @foreach ($objPro as $item)
                <option id="id_product_ajax" value="{{ $item->product_id }}" >{{ $item->name }}
                </option>
                @endforeach
              </select> 
              </div>

              @if ($errors->has('idProduct'))
              <span class="text-sm">
                <i class="fa fa-info-circle"></i> {{ $errors->first('type') }}
              </span>
              @endif

            </div>
          </div>

          <div class="form-group row {{ $errors->has('price1') ? ' text-red' : '' }}">
            <label for="name" class="col-sm-3 col-form-label">{{ sc_language_render('admin.productprice.teacher') }}</label>
            <div class="col-sm-9 ">
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                </div>
                <input type="number" id="price_product1" name="price1" value="{{ old('price1') }}" class="form-control name {{ $errors->has('price1') ? ' is-invalid' : '' }}">
              </div>

              @if ($errors->has('price1'))
              <span class="text-sm">
                <i class="fa fa-info-circle"></i> {{ $errors->first('price1') }}
              </span>
              @endif

            </div>
          </div>

          <div class="form-group row {{ $errors->has('price2') ? ' text-red' : '' }}">
            <label for="name" class="col-sm-3 col-form-label">{{ sc_language_render('admin.productprice.child') }}</label>
            <div class="col-sm-9 ">
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                </div>
                <input type="number" id="price_product2" name="price2" value="{!! old('price2') !!}" class="form-control value {{ $errors->has('price2') ? ' is-invalid' : '' }}">
              </div>

              @if ($errors->has('price2'))
              <span class="text-sm">
                <i class="fa fa-info-circle"></i> {{ $errors->first('price2') }}
              </span>
              @endif

            </div>
          </div>


        </div>
        <!-- /.card-body -->
       
        <div class="card-footer">
          <button type="submit"  class="btn btn-success float-right"><i class="fa fa-plus"></i> {{ sc_language_render('product.admin.add_new') }}</button>
        </div>
      </form>
    </div>
  </div>


  <div class="col-md-6">

    <div class="card">
      <div class="card-header">
        <div class="col-sm-5 float-left">
          <h3 class="card-title"><i class="fas fa-th-list"></i> {!! $title_description ?? '' !!}</h3>
        </div>
        
        <div class=" float-right">
          <div class="card-tools">
            @if (!empty($topMenuRight) && count($topMenuRight))
              @foreach ($topMenuRight as $item)
                  <div class="menu-right">
                    @php
                        $arrCheck = explode('view::', $item);
                    @endphp
                    @if (count($arrCheck) == 2)
                      @if (view()->exists($arrCheck[1]))
                        @include($arrCheck[1])
                      @endif
                    @else
                      {!! trim($item) !!}
                    @endif
                  </div>
              @endforeach
            @endif
          </div>
        </div>
        
      </div>

      <div class="card-body p-0">
            <section id="pjax-container" class="table-list">
              <div class="box-body table-responsivep-0" >
                 <table class="table table-hover box-body text-wrap table-bordered">
                    <thead>
                       <tr>
                        @if (!empty($removeList))
                        <th></th>
                        @endif
                        @foreach ($listTh as $key => $th)
                            <th>{!! $th !!}</th>
                        @endforeach
                       </tr>
                    </thead>
                    <tbody>
                        @foreach ($dataTr as $keyRow => $tr)
                            <tr class="{{ (request('id') == $keyRow) ? 'active': '' }}">
                                @if (!empty($removeList))
                                <td>
                                  <input class="checkbox" type="checkbox" class="grid-row-checkbox" data-id="{{ $keyRow }}">
                                </td>
                                @endif
                                @foreach ($tr as $key => $trtd)
                                    <td>{!! $trtd !!}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                 </table>
                 <div class="block-pagination clearfix m-10">
                  <div class="ml-3 float-left">
                    {!! $resultItems??'' !!}
                  </div>
                  <div class="pagination pagination-sm mr-3 float-right">
                    {!! $pagination??'' !!}
                  </div>
                </div>
              </div>
             </section>
    </div>

    </div>
  </div>

</div>
</div>

@endsection
@push('scripts')
    {{-- //Pjax --}}
{{-- <script src="{{ sc_file('admin/plugin/jquery.pjax.js')}}"></script> --}}
  <script type="text/javascript">

    $('.grid-refresh').click(function(){
      $.pjax.reload({container:'#pjax-container'});
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
    $(function(){
     $(document).pjax('a.page-link', '#pjax-container')
    })


    $(document).ready(function(){
    // does current browser support PJAX
      if ($.support.pjax) {
        $.pjax.defaults.timeout = 2000; // time in milliseconds
      }
    });

    @if ($buttonSort)
      $('#button_sort').click(function(event) {
        var url = '{{ $urlSort??'' }}?sort_shipping='+$('#shipping_sort option:selected').val();
        $.pjax({url: url, container: '#pjax-container'})
      });
    @endif

  </script>
    {{-- //End pjax --}}


<script type="text/javascript">
{{-- sweetalert2 --}}
var selectedRows = function () {
    var selected = [];
    $('.grid-row-checkbox:checked').each(function(){
        selected.push($(this).data('id'));
    });

    return selected;
}

$('.grid-trash').on('click', function() {
  var ids = selectedRows().join();
  deleteItem(ids);
});

  function deleteItem(ids){
  Swal.mixin({
    customClass: {
      confirmButton: 'btn btn-success',
      cancelButton: 'btn btn-danger'
    },
    buttonsStyling: true,
  }).fire({
    title: '{{ sc_language_render('action.delete_confirm') }}',
    text: "",
    type: 'warning',
    showCancelButton: true,
    confirmButtonText: '{{ sc_language_render('action.confirm_yes') }}',
    confirmButtonColor: "#DD6B55",
    cancelButtonText: '{{ sc_language_render('action.confirm_no') }}',
    reverseButtons: true,

    preConfirm: function() {
        return new Promise(function(resolve) {
            $.ajax({
                method: 'delete',
                url: '{{ $urlDeleteItem ?? '' }}',
                data: {
                  ids:ids,
                    _token: '{{ csrf_token() }}',
                },
                success: function (data) {
                    if(data.error == 1){
                      alertMsg('error', data.msg, '{{ sc_language_render('action.warning') }}');
                      $.pjax.reload('#pjax-container');
                      return;
                    }else{
                      alertMsg('success', data.msg);
                      window.location.replace('{{ sc_route_admin('admin_price.add',['id'=>$objProPrice->id]) }}');
                    }

                }
            });
        });
    }

  }).then((result) => {
    if (result.value) {
      alertMsg('success', '{{ sc_language_render('action.delete_confirm_deleted_msg') }}', '{{ sc_language_render('action.delete_confirm_deleted') }}');
    } else if (
      // Read more about handling dismissals
      result.dismiss === Swal.DismissReason.cancel
    ) {
      // swalWithBootstrapButtons.fire(
      //   'Cancelled',
      //   'Your imaginary file is safe :)',
      //   'error'
      // )
    }
  })
}


</script>

<!-- Ediable -->
<script src="{{ sc_file('admin/plugin/bootstrap-editable.min.js')}}"></script>
<script type="text/javascript">
  // Editable
  $(document).ready(function() {

    //  $.fn.editable.defaults.mode = 'inline';
    $.fn.editable.defaults.params = function (params) {
      params._token = "{{ csrf_token() }}";
      params.lang = "{{ 'dump' }}";
      return params;
    };

    $('.editable-required').editable({
      validate: function(value) {
          if (value == '') {
              return '{{  sc_language_render('admin.not_empty') }}';
          }
      },
      success: function(data) {
        if(data.error == 0){
          alertJs('success', '{{ sc_language_render('admin.msg_change_success') }}');
        } else {
          alertJs('error', data.msg);
        }
    }
    });

});
</script>
@endpush

