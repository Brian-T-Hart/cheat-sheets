<?php

add_action('wp_footer', 'push_view_product_to_datalayer');
function push_view_product_to_datalayer() {
    if (!is_product()) return;

    global $product;

    if (!$product || !is_a($product, 'WC_Product')) return;

    // Get product data
    $product_id   = $product->get_id();
    $sku          = $product->get_sku();
    $name         = $product->get_name();
    $price        = $product->get_price();
    $currency     = get_woocommerce_currency();

    echo "<script>
        window.dataLayer = window.dataLayer || [];
        dataLayer.push({
            event: 'view_item',
            ecommerce: {
                currency: '{$currency}',
                value: {$price},
                items: [{
                    item_id: '{$sku}',
                    item_name: '" . esc_js($name) . "',
                    price: {$price},
                    quantity: 1
                }]
            }
        });
    </script>";
}