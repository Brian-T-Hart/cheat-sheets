<?php

add_action('wp_footer', 'push_view_cart_to_datalayer');

function push_view_cart_to_datalayer()
{
    if (! is_cart()) return;
    $cart = WC()->cart->get_cart();
    $items = [];

    foreach ($cart as $cart_item) {
        $product = $cart_item['data'];
        $items[] = [
            'item_id' => $product->get_sku(),
            'item_name' => $product->get_name(),
            'quantity' => $cart_item['quantity'],
            'price' => $product->get_price(),
        ];
    }

    $currency = get_woocommerce_currency();
    $value = WC()->cart->get_total('raw');

    echo "<script>
            window.dataLayer = window.dataLayer || [];
            dataLayer.push({
                event: 'view_cart',
                test: 'test',
                ecommerce: {
                    currency: '{$currency}',
                    value: {$value},
                    items: " . json_encode($items) . "
                }
            });
        </script>";
}
