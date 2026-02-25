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
        $this->controls['video_url_desktop'] = [
            'tab'   => 'content',
            'label' => esc_html__('Desktop Video URL', 'bricks'),
            'type'  => 'text',
            'placeholder' => 'https://example.com/video.mp4',
            'description' => esc_html__('Enter the URL of the video to be displayed on desktop. If left empty, no video will display.', 'bricks'),
        ];

        $this->controls['video_url_mobile'] = [
            'tab'   => 'content',
            'label' => esc_html__('Mobile Video URL', 'bricks'),
            'type'  => 'text',
            'placeholder' => 'https://example.com/video-mobile.mp4',
            'description' => esc_html__('Enter the URL of the video to be displayed on mobile. If left empty, no video will display.', 'bricks'),
        ];

        $this->controls['poster_image'] = [
            'tab'   => 'content',
            'label' => esc_html__('Poster Image URL', 'bricks'),
            'type'  => 'text',
            'placeholder' => 'https://example.com/poster.jpg',
            'description' => esc_html__('Enter the URL of the poster image to be displayed before the video loads.', 'bricks'),
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

        $this->controls['breakpoint'] = [
            'tab'   => 'responsive',
            'label' => esc_html__('Mobile Breakpoint', 'bricks'),
            'type'  => 'number',
            'placeholder' => '768',
            'description' => esc_html__('Viewport width in pixels where mobile view displays.', 'bricks'),
        ];
    }

    public function render()
    {
        $settings = $this->settings;

        if (empty($settings['video_url_desktop']) && empty($settings['video_url_mobile'])) return;

        $video_url_desktop = esc_url($settings['video_url_desktop'] ?? '');
        $video_url_mobile = esc_url($settings['video_url_mobile'] ?? '');
        $poster_image = esc_url($settings['poster_image'] ?? '');
        $video_trigger = $settings['load_trigger'] ?? 'load';
        $video_breakpoint = intval($settings['breakpoint'] ?? 768);

        // Set the video URLs as data attributes for lazy loading
        if (! empty($video_url_desktop)) {
            $this->set_attribute('_root', 'data-src-desktop', $video_url_desktop);
        }

        if (! empty($video_url_mobile)) {
            $this->set_attribute('_root', 'data-src-mobile', $video_url_mobile);
        }

        if (! empty($poster_image)) {
            $this->set_attribute('_root', 'poster', $poster_image);
        }

        $this->set_attribute('_root', 'data-trigger', $video_trigger);
        $this->set_attribute('_root', 'data-breakpoint', $video_breakpoint);
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
