<script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
<script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
<script src="https://unpkg.com/filepond/dist/filepond.min.js"></script>
<script src="https://unpkg.com/jquery-filepond/filepond.jquery.js"></script>
<script>
    $.fn.filepond.registerPlugin(FilePondPluginFileValidateSize);
    $.fn.filepond.registerPlugin(FilePondPluginFileValidateType);
    $.fn.filepond.registerPlugin(FilePondPluginImagePreview);
    $('#button-upload').prop('disabled', true);
    $('.filepond').filepond({
        labelIdle: 'Chọn từ hệ thống hoặc kéo thả file vào <span class="filepond--label-action"> đây</span>',
        maxFileSize: '10MB',
        acceptedFileTypes: [
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ],
        storeAsFile: true,
        allowImagePreview: true
    });
    $('.filepond').on('FilePond:updatefiles', function (e) {
        if (e.detail.items.length > 0) {
            $('#button-upload').prop('disabled', false);
        } else {
            $('#button-upload').prop('disabled', true);
        }
    });
    $(document).ready(function () {
        $('#import-excel').submit(function () {
            $('#loading').show();
        });
    });
</script>