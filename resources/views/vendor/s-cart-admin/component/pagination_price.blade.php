<ul class="pagination pagination-sm no-margin pull-right">
    <!-- Previous Page Link -->
    @if ($paginator->onFirstPage())
    <li class="page-item disabled"><span class="page-link pjax-container">&laquo;</span></li>
    @else
    <li class="page-item"><a class="page-link pjax-container" href="{{ $paginator->previousPageUrl() }}" rel="prev">&laquo;</a></li>
    @endif

    <!-- Pagination Elements -->
    @foreach ($elements as $element)
    <!-- "Three Dots" Separator -->
    @if (is_string($element))
    <li class="page-item disabled"><span class="page-link pjax-container">{{ $element }}</span></li>
    @endif

    <!-- Array Of Links -->
    @if (is_array($element))
    @foreach ($element as $page => $url)
    @if ($page == $paginator->currentPage())
    <li class="page-item active"><span class="page-link pjax-container">{{ $page }}</span></li>
    @else
    <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
    @endif
    @endforeach
    @endif
    @endforeach

    <!-- Next Page Link -->
    @if ($paginator->hasMorePages())
    <li class="page-item"><a class="page-link pjax-container" href="{{ $paginator->nextPageUrl() }}" rel="next">&raquo;</a></li>
    @else
    <li class="page-item disabled"><span class="page-link pjax-container">&raquo;</span></li>
    @endif
</ul>

<!-- Ediable -->
<script src="{{ sc_file('admin/plugin/bootstrap-editable.min.js') }}"></script>
<script type="text/javascript">
    // Editable
    $(document).ready(function() {

        $.fn.editable.defaults.params = function(params) {
            params._token = "{{ csrf_token() }}";
            params.lang = "{{ 'dump' }}";
            return params;
        };

        $('.editable-required').editable({
            validate: function(value) {
                if (value == '') {
                    return '{{ sc_language_render('admin.not_empty') }}';
                }
                if(/\D/.test(value)) {
                    return '{{  sc_language_render('product.price.no.negative') }}';
                }
            },
            success: function(data) {
                if (data.error == 0) {
                    alertJs('success', '{{ sc_language_render('admin.msg_change_success') }}');
                } else {
                    alertJs('error', data.msg);
                }
            },
            display: function(value, response) {
                let a = Number(value);
                let x = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(a);
                $(this).text( x);
            },
        });

    });
</script>