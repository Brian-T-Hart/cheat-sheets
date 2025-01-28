<?php

if (!class_exists('ProductionScripts')) {
    class ProductionScripts
    {
        private $site_url;
        private $is_production;
        private $production_domain = 'production-site.com';

        public function __construct()
        {
            $this->site_url = get_site_url();
            $this->is_production = strpos($this->site_url, $this->production_domain) !== false;

            if ($this->is_production) {
                add_action('wp_head', [$this, 'add_production_scripts_to_head'], 15);
                add_action('wp_footer', [$this, 'add_production_scripts_to_footer']);
            }
        }

        /**
         * Add production scripts to the head
         */
        public function add_production_scripts_to_head()
        {
            ob_start();
            ?>

            <!-- Add Production Header Scripts Here -->

            <?php
            echo ob_get_clean();
        }

        /**
         * Add production scripts to the footer
         */
        public function add_production_scripts_to_footer()
        {
            ob_start();
            ?>

            <!-- Add Production Footer Scripts Here -->
             
            <?php
            echo ob_get_clean();
        }
    }
}

new ProductionScripts();
