<?php
$if_price_is_zero_shortcode_field = get_option('if_price_is_zero_shortcode_field');
$if_price_is_zero_button_name = get_option('if_price_is_zero_button_name');
$catalog = get_option('if_price_is_zero_catalog_on');


add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_style( 'wepo_frontend_styles', plugins_url( 'assets/css/frontend.min.css' , __FILE__ ));
    wp_enqueue_script( 'wepo_frontend_scripts', plugins_url( 'assets/js/frontend.min.js' , __FILE__ ), false, null, true );
}, 10020);

// когда включен режим каталога, делает редирект на главную страницу со страницы корзины и оформления заказа
add_action( 'template_redirect', function() {
    $option = get_option('if_price_is_zero_catalog_on');
    if($option == 'on' && (is_checkout() || is_cart())){
        wp_redirect(site_url(), 302);
        exit;
    }
});

// модальное окно
add_action( 'wp_footer', function() {
   global $if_price_is_zero_shortcode_field;
    if($if_price_is_zero_shortcode_field) $if_price_is_zero_shortcode_field = str_replace(["\\"], "", $if_price_is_zero_shortcode_field);
    if(wepo_check_plugin_fields()){ ?>
        <div id="wepo_woo_product_modal">
            <div class="modal__content">
                <?php echo do_shortcode($if_price_is_zero_shortcode_field); ?>
                <a href="#" class="modal__close">&times;</a>
            </div>
        </div>
    <?php }
});

// если цены нет, то выводить надпись "Цену уточняйте"
add_filter( 'woocommerce_get_price_html', 'wepo_woo_product_price_empty', 100, 2 );
function wepo_woo_product_price_empty( $price, $product ){
    if (wepo_check_plugin_fields() && !$product->is_type( 'variable' ) ) {
        if (empty($price) || intval($product->price) == 0) {
            $price = '<span class="woocommerce-Price-amount amount">Цену уточняйте</span>';
        }
    }
    return $price;
}

// проверить заполнены ли поля
function wepo_check_plugin_fields(){
    global $if_price_is_zero_button_name, $if_price_is_zero_shortcode_field;
    if($if_price_is_zero_button_name && $if_price_is_zero_shortcode_field) return true;
    return false;
}

// страница товара
add_action('woocommerce_single_product_summary', 'wepo_woo_single_product_summary', 2  );
function wepo_woo_single_product_summary(){
    global $product;
    if(!$product->is_type( 'variable' ) && !$product->is_type( 'simple' )) return;
    if(wepo_check_plugin_fields()){
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
        add_action('woocommerce_single_product_summary', 'wepo_woo_template_single_add_to_cart', 30);
    }
}
function wepo_woo_template_single_add_to_cart() {
    global $product, $catalog, $if_price_is_zero_button_name;
    $p_disable_cart = get_post_meta( $product->get_id(), '_disable_cart', true );
    if ( $product->is_type( 'variable' ) ) {
        do_action( 'woocommerce_' . $product->get_type() . '_add_to_cart' );
        echo "<a href='#wepo_woo_product_modal' class='btn btn-color-primary btn-size-large if_price_is_zero_plugin' style='display: none'>" . $if_price_is_zero_button_name . "</a><br>";
    }
    if ( $product->is_type( 'simple' ) ) {
        if ((wepo_check_plugin_fields() && (empty($product->price) || intval($product->price) == 0)) || $catalog == 'on' || $p_disable_cart == 'yes') {
            echo "<a href='#wepo_woo_product_modal' class='btn btn-color-primary btn-size-large if_price_is_zero_plugin'>" . $if_price_is_zero_button_name . "</a><br>";
        } else {
            do_action( 'woocommerce_' . $product->get_type() . '_add_to_cart' );
        }
    }
}

// каталог товаров
add_action('woocommerce_after_shop_loop_item', 'wepo_custom_woo_after_shop_loop_item', 2  );
function wepo_custom_woo_after_shop_loop_item(){
    global $product, $catalog;
    $p_disable_cart = get_post_meta( $product->get_id(), '_disable_cart', true );
    if(!$product->is_type( 'variable' ) && !$product->is_type( 'simple' )) return;
    if((wepo_check_plugin_fields() && (empty($product->price) || intval($product->price) == 0)) || $catalog == 'on' || $p_disable_cart == 'yes') {
        remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
        add_action('woocommerce_after_shop_loop_item', 'wepo_woo_template_loop_add_to_cart', 20 );
        echo "<style>.product[data-id='".$product->id."'] .add-to-cart-loop{display: none !important;}</style>";
    }
}
function wepo_woo_template_loop_add_to_cart() {
    global $product, $catalog;
    $p_disable_cart = get_post_meta( $product->get_id(), '_disable_cart', true );
    if(!$product->is_type( 'variable' ) && !$product->is_type( 'simple' )) return;
    if ((wepo_check_plugin_fields() && (empty($product->price) || intval($product->price) == 0)) || $catalog == 'on' || $p_disable_cart == 'yes') {
        echo "<div class='wd-add-btn wd-add-btn-replace'>";
        echo "<a href='". esc_url(get_permalink($product->ID)) ."' class='button'>Подробнее</a>";
        echo "</div>";
    } else {
        woocommerce_template_loop_add_to_cart();
    }
}

// минимальное кол-во товара для заказа, а также кол-во единиц за один шаг 
add_filter( 'woocommerce_quantity_input_args', 'wepo_woo_quantity_input_args', 10, 2 );
function wepo_woo_quantity_input_args( $args, $product ) {
    $order_step = get_post_meta( $product->get_id(), '_order_step', true );
    $min_order_count = get_post_meta( $product->get_id(), '_min_order_count', true );
    if($min_order_count){
        $args['input_value'] = is_cart() ? $args['input_value'] : $min_order_count;
        $args['min_value']   = $min_order_count;
    }
    if($order_step){
        $args['step'] = $order_step;
    }
    return $args;
}

//проверка на минимальное кол-во товара для заказа на странице товара
add_filter( 'woocommerce_quantity_input_min', 'wepo_woo_min_order_count', 20, 2 );
function wepo_woo_min_order_count( $min, $product ){
    $min_order_count = get_post_meta( $product->get_id(), '_min_order_count', true );
    if ($min_order_count) $min = $min_order_count;
    return $min;
}

//проверка на минимальное кол-во товара для заказа в корзине
add_filter( 'woocommerce_cart_item_quantity', 'wepo_woo_min_order_count_cart', 20, 3 );
function wepo_woo_min_order_count_cart( $product_quantity, $cart_item_key, $cart_item ) {
    $product = $cart_item['data'];
    $min_order_count = get_post_meta( $product->get_id(), '_min_order_count', true );
    $min = 0;
    if ($min_order_count) $min = $min_order_count;
    return woocommerce_quantity_input(
        array(
            'input_name'   => "cart[{$cart_item_key}][qty]",
            'input_value'  => $cart_item['quantity'],
            'max_value'    => $product->get_max_purchase_quantity(),
            'min_value'    => $min,
            'product_name' => $product->get_name(),
        ),
        $product,
        false
    );
}





