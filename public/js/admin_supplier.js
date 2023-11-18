$(document).ready(function () {
    initEdit();
    if(supplierProductList > 0){
        renderProductFromJson();
    }
    $('#btn_add_product').click(function () {
        let productKey = selectProduct.val()
        let productName = selectProduct.find(":selected").text();

        if(productListKey.includes(productKey)){
            alert('Sản phẩm này đã trùng');
        } else {
            productListKey.push(productKey);
            productUl.append(createProductLi(productKey, productName, true))
            productCount.text(productListKey.length);
        }
        productListKeyToInput();
    });
});

function initEdit(){
    let output = [];
    if(supplierProductList.length > 0){
        for(let i = 0; i < supplierProductList.length; i++){
            output.push(supplierProductList[i].id);
        }
    }
    productListKey = output;
    productListKeyToInput();
    renderProductFromJson();
    productCount.text(productListKey.length);
}

function removeProduct(productKey) {
    if(productListKey.includes(productKey)){
        productListKey.splice(productListKey.indexOf(productKey), 1);
        $('#'+productKey).remove();
        productCount.text(productListKey.length);
        productListKeyToInput();
        return true;
    } else {
        alert('Sản phẩm này chưa tồn tại trong danh sách');
        return false;
    }
}

function renderProductFromJson(){
    for(let i = 0; i < supplierProductList.length; i++){
        productUl.append(createProductLi(supplierProductList[i].id, supplierProductList[i].name));
    }
}

function createProductLi(id, name, isnew = false){
    let html = '';
    if(!isnew){
        html = '<li id="' +id+ '" class="list-group-item"><button type="button" onclick="removeProduct(\'' + id + '\')" class="btn btn-sm"><i class="fas fa-times-circle text-red"></i></button>'+ name +'</li>';
    } else {
        html = '<li id="' +id+ '" class="list-group-item" style="background-color: rgb(211,236,217)"><button type="button" onclick="removeProduct(\'' + id + '\')" class="btn btn-sm"><i class="fas fa-times-circle text-red"></i></button>'+ name +'</li>';
    }
    let productLi = htmlToElement(html);
    return productLi;
}

function productListKeyToInput(){
    $('#product_key_input').val(productListKey.join('|'));
}

function htmlToElement(html) {
    var template = document.createElement('template');
    html = html.trim(); // Never return a text node of whitespace as the result
    template.innerHTML = html;
    return template.content.firstChild;
}