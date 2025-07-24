<?php

add_action('woocommerce_before_checkout_form', 'push_checkout_data_to_datalayer');

function push_checkout_data_to_datalayer()
{
    if (!is_checkout()) return;

    $items = [];
    foreach (WC()->cart->get_cart() as $cart_item) {
        $product = $cart_item['data'];
        $items[] = [
            'item_id'   => $product->get_sku(),
            'item_name' => $product->get_name(),
            'price'     => $product->get_price(),
            'quantity'  => $cart_item['quantity']
        ];
    }
?>
    <script>
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({
            event: "begin_checkout",
            ecommerce: {
                items: <?php echo json_encode($items); ?>
            }
        });
    </script>
<?php
}
