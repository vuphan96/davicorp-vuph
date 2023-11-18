
<script type="text/javascript">

  $(function () {
    $('input.checkbox').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' /* optional */
    });
  });

  $(document).on('ready pjax:end', function(event) {
    $('input.checkbox').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square-blue',
        increaseArea: '20%' /* optional */
      });
  })

</script>

<script>
    $(function () {
        $.datepicker.regional['vi'] = {
            closeText: 'Đóng',
            prevText: '&#x3c;Trước', prevStatus: '',
            nextText: 'Tiếp&#x3e;', nextStatus: '',
            currentText: 'Hôm nay', currentStatus: '',
            monthNames: ['Tháng Một', 'Tháng Hai', 'Tháng Ba', 'Tháng Tư', 'Tháng Năm', 'Tháng Sáu',
                'Tháng Bảy', 'Tháng Tám', 'Tháng Chín', 'Tháng Mười', 'Tháng Mười Một', 'Tháng Mười Hai'],
            monthNamesShort: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6',
                'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'],
            dayNames: ['Chủ Nhật', 'Thứ Hai', 'Thứ Ba', 'Thứ Tư', 'Thứ Năm', 'Thứ Sáu', 'Thứ Bảy'],
            dayNamesShort: ['CN', 'Hai', 'Ba', 'Tư', 'Năm', 'Sáu', 'Bảy'],
            dayNamesMin: ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'],
            weekHeader: 'Bảy',
            firstDay: 1,
            isRTL: false,
            showMonthAfterYear: false,
            yearSuffix: ''};
        $.datepicker.setDefaults($.datepicker.regional['vi']);
        $(".date_time").datepicker({ dateFormat: "yy-mm-dd" });
    });
</script>

{{-- image file manager --}}
<script type="text/javascript">
(function( $ ){

      $.fn.filemanager = function(type, options) {
        type = type || 'other';

        this.on('click', function(e) {
          type = $(this).data('type') || type;//sc
          var route_prefix = (options && options.prefix) ? options.prefix : '{{ sc_route_admin('admin.home').'/'.config('lfm.url_prefix') }}';
          var target_input = $('#' + $(this).data('input'));
          var target_preview = $('#' + $(this).data('preview'));
          window.open(route_prefix + '?type=' + type, '{{ sc_language_render('admin.file_manager') }}', 'width=900,height=600');
          window.SetUrl = function (items) {
            var file_path = items.map(function (item) {
              return item.url;
            }).join(',');

            // set the value of the desired input to image url
            target_input.val('').val(file_path).trigger('change');

            // clear previous preview
            target_preview.html('');

            // set or change the preview image src
            items.forEach(function (item) {
              target_preview.append(
                $('<img>').attr('src', item.thumb_url)
              );
            });

            // trigger change event
            target_preview.trigger('change');
          };
          return false;
        });
      }

    })(jQuery);

    $('.lfm').filemanager();
</script>
{{-- //image file manager --}}


<script type="text/javascript">
// Select row
  $(function () {
    //Enable check and uncheck all functionality
    $(".grid-select-all").click(function () {
      var clicks = $(this).data('clicks');
      if (clicks) {
        //Uncheck all checkboxes
        $(".box-body input[type='checkbox']").iCheck("uncheck");
        $(".far", this).removeClass("fa-check-square").addClass('fa-square');
      } else {
        //Check all checkboxes
        $(".box-body input[type='checkbox']").iCheck("check");
        $(".far", this).removeClass("fa-square").addClass('fa-check-square');
      }
      $(this).data("clicks", !clicks);
    });

  });
// == end select row

  function format_number(n) {
      return n.toFixed(0).replace(/./g, function(c, i, a) {
          return i > 0 && c !== "." && (a.length - i) % 3 === 0 ? "," + c : c;
      });
  }

// active tree menu
$('.nav-treeview > li.active').parents('.has-treeview').addClass('active menu-open');
// ==end active tree menu

</script>

<script>
    function LA() {}
    LA.token = "{{ csrf_token() }}";

    function alertJs(type = 'error', msg = '') {
      const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
      });
      Toast.fire({
        type: type,
        title: msg
      })
    }

    function alertMsg(type = 'error', msg = '', note = '') {
      const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
          confirmButton: 'btn btn-success',
          cancelButton: 'btn btn-danger'
        },
        buttonsStyling: true,
      });
      swalWithBootstrapButtons.fire(
        msg,
        note,
        type
      )
    }

    function alertConfirm(type = 'warning', msg = '') {
      const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
      });
      Toast.fire({
        type: type,
        title: msg
      })
    }
    $('[data-toggle="tooltip"]').tooltip();
    $('.select2').select2();

    function hasPerm(perm){
        @if(\Admin::user()->isAdministrator())
            return true;
        @else
            @php
                $userPerms = json_encode(data_get(\Admin::user()->allPermissions()->toArray(), "*.slug"));
                echo('let userPerm = '.$userPerms. ';')
            @endphp
            return userPerm.includes(perm);
        @endif
    }

    function grantPerm(){
        $('[data-perm]:not([data-perm=""])').each(function(){
            let dataPerm = $(this).attr('data-perm');
            let permType = $(this).attr('perm-type');
            if (dataPerm && !hasPerm(dataPerm)){
                if (permType && permType == 'disable'){
                    $(this).css('pointer-events', 'none');
                } else{
                    $(this).hide();
                }

            }
        });
    }



    $(document).ready(function () {
        let btn = $('button[type="reset"]');
        btn.click(function () {
            let select = $('.select2').not('.select_supplier, .select_product');
            for(let i = 0; i < select.length; i++){
                let element = $(select[i]);
                element.val(element.find('option[selected]').val()).trigger('change');
            }
        });
        setTimeout(()=> {document.getElementsByClassName("nav-item active")[0].scrollIntoView({block: 'center', inline: 'center'});}, 1000);


        //
        // grantPerm();


    });

    $(document).on('pjax:end', function(event) {
        grantPerm();
    }).trigger('pjax:end');

</script>