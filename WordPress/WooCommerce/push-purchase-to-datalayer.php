<?php

add_action('woocommerce_thankyou', 'push_purchase_event_to_datalayer', 10, 1);

function push_purchase_event_to_datalayer($order_id)
{
    if (!$order_id) return;

    $order = wc_get_order($order_id);

    if (!$order) return;

    $items = [];

    foreach ($order->get_items() as $item) {
        $product = $item->get_product();
        $items[] = [
            'item_id'   => $product->get_sku(),
            'item_name' => $product->get_name(),
            'price'     => $product->get_price(),
            'quantity'  => $item->get_quantity()
        ];
    }

    $data = [
        'event' => 'purchase',
        'ecommerce' => [
            'transaction_id' => esc_js($order->get_order_number()),
            'value'          => esc_js($order->get_total()),
            'currency'       => esc_js($order->get_currency()),
            'items'          => $items
        ]
    ];

?>
    <script>
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push(<?php echo wp_json_encode($data); ?>);
    </script>
<?php
}
