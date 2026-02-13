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
    }

    public function render()
    {
        $settings = $this->settings;

        if (empty($settings['video_url'])) return;

        $video_url = esc_url($settings['video_url']);

        // Set the video URL as a data attribute for lazy loading
        $this->set_attribute('_root', 'data-src', $video_url);

        // Background-video default attributes
        $this->set_attribute('_root', 'autoplay');
        $this->set_attribute('_root', 'muted');
        $this->set_attribute('_root', 'loop');
        $this->set_attribute('_root', 'playsinline');
        $this->set_attribute('_root', 'preload', 'none');

        echo '<video ' . $this->render_attributes('_root') . '></video>';
    }
}
