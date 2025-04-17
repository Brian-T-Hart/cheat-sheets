<?php

add_action('wp_footer', 'add_to_cart_datalayer_script');
function add_to_cart_datalayer_script() {
    ?>
    <script>
    window.dataLayer = window.dataLayer || [];

    // Listen for clicks on any .add_to_cart_button
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.add_to_cart_button');

        if (btn) {
            // Basic product data from button's data attributes
            const productId = btn.dataset.product_id;
            const productSku = btn.dataset.sku || '';
            const productName = btn.getAttribute('aria-label') || 'Product';

            // You may want to fetch more details via AJAX or preload them in data attributes

            dataLayer.push({
                event: 'add_to_cart',
                ecommerce: {
                    items: [{
                        item_id: productSku || productId,
                        item_name: productName,
                        quantity: 1
                    }]
                }
            });

            console.log(dataLayer); // For debugging purposes
        }
    });
    </script>
    <?php
}