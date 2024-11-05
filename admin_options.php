<?php

$page_slug = 'woo_extra_product_options';
$option_group = 'woo_extra_product_options_admin';

add_action( 'admin_menu', 'wepo_woo_add', 25 );
add_filter( 'option_page_capability_'.$page_slug, 'wepo_woo_my_page_capability' );


function wepo_woo_add(){
    global $page_slug;
    add_menu_page('WooCommerce Extra Product Options', 'WooCommerce Extra Product Options', 'manage_options', $page_slug, 'wepo_woo_display', 'dashicons-cart', 4 );
}

function wepo_woo_display( $args ){
    if (isset($_POST['submit'])){
        $if_price_is_zero_shortcode_field = trim($_POST['if_price_is_zero_shortcode_field']);
        $if_price_is_zero_button_name = trim($_POST['if_price_is_zero_button_name']);
        $if_price_is_zero_catalog_on = trim($_POST['if_price_is_zero_catalog_on']);
        update_option('if_price_is_zero_shortcode_field', $if_price_is_zero_shortcode_field);
        update_option('if_price_is_zero_button_name', $if_price_is_zero_button_name);
        update_option('if_price_is_zero_catalog_on', $if_price_is_zero_catalog_on);
    }
    settings_fields("opt_group");     
    require plugin_dir_path(__FILE__) . '/admin_template.php';
}

function wepo_woo_my_page_capability( $capability ) {
    return 'edit_others_posts';
}

add_action('woocommerce_product_options_general_product_data', 'wepo_woo_shop_add_custom_fields');
function wepo_woo_shop_add_custom_fields() {
    echo '<div class="options_group">';
    woocommerce_wp_checkbox( array(
        'id'                => '_disable_cart',
        'label'             => __( 'Отключить кнопку "В корзину"', 'woocommerce' ),
        'placeholder'       => '',
        'desc_tip'          => 'true',
        'custom_attributes' => array(),
        'description'       => __( 'Отключите кнопку "В корзину", если хотите вывести вместо нее кнопку "Оставить заявку" с контактной формой', 'woocommerce' ),
    ) );
    echo '</div>';
    echo '<div class="options_group">';
    woocommerce_wp_text_input( array(
        'id'                => '_min_order_count',
        'label'             => __( 'Минимальное кол-во товара для заказа', 'woocommerce' ),
        'placeholder'       => '',
        'type'              => 'number',
        'custom_attributes' => array(
            'step' => 'any',
            'min'  => '0',
        ),
    ) );
    woocommerce_wp_text_input( array(
        'id'                => '_order_step',
        'label'             => __( 'Количество товара за один шаг', 'woocommerce' ),
        'placeholder'       => '',
        'type'              => 'number',
        'custom_attributes' => array(
            'step' => 'any',
            'min'  => '0',
        ),
    ) );
    echo '</div>';
}

add_action( 'woocommerce_process_product_meta', 'wepo_woo_custom_fields_save', 10 );
function wepo_woo_custom_fields_save( $post_id ) {
    $product = wc_get_product( $post_id );

    $checkbox_field = isset( $_POST['_disable_cart'] ) ? 'yes' : 'no';
    $min_order_count_field = isset( $_POST['_min_order_count'] ) ? sanitize_text_field( $_POST['_min_order_count'] ) : '';
    $order_step_field = isset( $_POST['_order_step'] ) ? sanitize_text_field( $_POST['_order_step'] ) : '';

    $product->update_meta_data( '_min_order_count', $min_order_count_field );
    $product->update_meta_data( '_order_step', $order_step_field );
    $product->update_meta_data( '_disable_cart', $checkbox_field );

    $product->save();
}





