<?php
if (! defined('ABSPATH')) exit; // Exit if accessed directly
if (! class_exists('\Bricks\Elements')) exit; // Exit if Bricks is not active

class Custom_Video_Element extends \Bricks\Element
{
    public $category     = 'media';
    public $name         = 'lazy-bg-video';
    public $icon         = 'ti-video-clapper';
    public $css_selector = '.brxe-lazy-bg-video';

    public function get_label()
    {
        return esc_html__('BG Video', 'bricks');
    }

    public function set_controls()
    {
        $this->controls['video_url'] = [
            'tab'   => 'content',
            'label' => esc_html__('Video URL', 'bricks'),
            'type'  => 'text',
            'placeholder' => 'https://example.com/video.mp4',
        ];

        $this->controls['load_trigger'] = [
            'tab'   => 'content',
            'label' => esc_html__('Load Trigger', 'bricks'),
            'type'  => 'select',
            'options' => [
                'load' => esc_html__('Window Loaded', 'bricks'),
                'interaction'  => esc_html__('User Interaction', 'bricks'),
            ],
        ];
    }

    public function render()
    {
        $settings = $this->settings;

        if (empty($settings['video_url'])) return;

        $video_url = esc_url($settings['video_url']);
        $video_trigger = $settings['load_trigger'] ?? 'load';

        // Set the video URL as a data attribute for lazy loading
        $this->set_attribute('_root', 'data-src', $video_url);
        $this->set_attribute('_root', 'data-trigger', $video_trigger);

        // Background-video default attributes
        $this->set_attribute('_root', 'autoplay');
        $this->set_attribute('_root', 'muted');
        $this->set_attribute('_root', 'loop');
        $this->set_attribute('_root', 'playsinline');
        $this->set_attribute('_root', 'preload', 'none');

        echo '<video ' . $this->render_attributes('_root') . '></video>';
    }

    public function enqueue_scripts()
    {
        wp_enqueue_style(
            'lazy-bg-video-style',
            get_stylesheet_directory_uri() . '/elements/lazy-bg-video/lazy-bg-video.css',
            array(),
            mt_rand()
        );

        wp_enqueue_script(
            'lazy-bg-video-script',
            get_stylesheet_directory_uri() . '/elements/lazy-bg-video/lazy-bg-video.js',
            array(),
            mt_rand()
        );
    }
}
