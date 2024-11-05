const form = document?.querySelector('form.variations_form')
if(form){
    form.addEventListener('change', function (event) {
        let prices = document.querySelectorAll('.single-product-page.product-type-variable .summary-inner .price .woocommerce-Price-amount bdi');
        if(prices.length === 1){
            if(prices[0].innerHTML.replace(/\D/g, '') === '0' || prices[0].innerHTML === 'Цену уточняйте'){
                document.querySelector('.single-product-page.product-type-variable .price .woocommerce-Price-amount bdi').innerHTML = 'Цену уточняйте';
                document.querySelector('.woocommerce-variation-add-to-cart').style.display = 'none';
                document.querySelector('.if_price_is_zero_plugin').style.display = 'inline-flex';
                document.querySelector('.variations_form').append(document.querySelector('.if_price_is_zero_plugin'));
            }
        } else {
            window.setInterval(function() {
                const price = document?.querySelector('.single_variation_wrap .single_variation .price .woocommerce-Price-amount');
                if(price){
                    if(price.innerHTML.replace(/\D/g, '') === '0' || price.innerHTML === 'Цену уточняйте'){
                        document.querySelector('.woocommerce-variation-add-to-cart').style.display = 'none';
                        document.querySelector('.if_price_is_zero_plugin').style.display = 'inline-flex';
                    } else {
                        document.querySelector('.woocommerce-variation-add-to-cart').style.display = 'inline-flex';
                        document.querySelector('.if_price_is_zero_plugin').style.display = 'none';
                    }
                }
            }, 50);
        }
    });
    form.dispatchEvent(new Event('change'));
}

const modal_button = document?.querySelector('a.if_price_is_zero_plugin')
const modal_close = document?.querySelector('#wepo_woo_product_modal .modal__close')
if(modal_button && modal_close){
    modal_button.addEventListener('click', function (event) {
        document.querySelector('html').classList.add("html-with-modal");
    })
    modal_close.addEventListener('click', function (event) {
        document.querySelector('html').classList.remove("html-with-modal");
    })
}