function wisestyle_update_cart_attribute(item){
    if(item){
        
        if(item.attributes && item.attributes.length){
            for (let index = 0; index < item.attributes.length; index++) {
                const attr = item.attributes[index];
                
                $('#cart-item-' + item.id + " .crazy-cart-item-attribute-value-" + attr.name).html(attr.text);
            }
        }
    }
}