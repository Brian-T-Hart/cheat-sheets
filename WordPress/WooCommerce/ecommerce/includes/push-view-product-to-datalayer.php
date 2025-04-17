<?php

add_action('wp_footer', 'push_view_product_to_datalayer');
function push_view_product_to_datalayer() {
    if (!is_product()) return;

    global $product;

    if (!$product || !is_a($product, 'WC_Product')) return;

    // Get product data
    $ecommerce_data = [
        'event' => 'view_item',
        'ecommerce' => [
            'currency' => get_woocommerce_currency(),
            'value'    => $product->get_price(),
            'items'    => [
                [
                    'item_id'   => $product->get_sku(),
                    'item_name' => $product->get_name(),
                    'price'     => $product->get_price(),
                    'quantity'  => 1,
                ],
            ],
        ],
    ];

    // Pass data to the script
    wp_enqueue_script('view-product-datalayer', YPM_ECOMMERCE_JS_DIR, [], null, true);
    wp_localize_script('view-product-datalayer', 'ecommerceData', $ecommerce_data);
}