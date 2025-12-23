<?php

function dev_print_style_handles()
{
    add_action('wp_print_styles', function () {
        if (str_starts_with($_SERVER['REQUEST_URI'], '/some-url')) {
            global $wp_styles;
            echo '<pre>';
            foreach ($wp_styles->queue as $handle) {
                echo $handle . "\n";
            }
            echo '</pre>';
        }
    });
}

function dev_print_script_handles()
{
    add_action('wp_print_scripts', function () {
        if (str_starts_with($_SERVER['REQUEST_URI'], '/some-url')) {
            global $wp_scripts;

            echo '<pre style="background:#111;color:#0f0;padding:10px;">';
            foreach ($wp_scripts->queue as $handle) {
                echo esc_html($handle) . "\n";
            }
            echo '</pre>';
        }
    });
}
