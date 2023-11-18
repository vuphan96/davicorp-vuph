function editableChecking(){
    console.log(`Number of items: ${  $('[order-lock]').length }`);
    $('[order-lock]').each(function(){
        let dataPerm = $(this).attr('order-lock') ?? "hide";
        console.log(dataPerm);
        if (dataPerm == 'disable'){
            $(this).css('pointer-events', 'none');
        } else {
            $(this).hide();
        }
    });
}

$(document).ready(function () {
    editableChecking();
})