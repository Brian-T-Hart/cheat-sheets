<?php
if (! defined('ABSPATH')) exit; // Exit if accessed directly
if (! class_exists('\Bricks\Elements')) exit; // Exit if Bricks is not active

class Lazy_Load_BG_Video_Element extends \Bricks\Element
{
    public $category     = 'media';
    public $name         = 'lazy-load-bg-video';
    public $icon         = 'ti-video-clapper';
    public $css_selector = '.brxe-lazy-load-bg-video';

    public function get_label()
    {
        return esc_html__('Lazy Load Background Video', 'bricks');
    }

    public function set_controls()
    {
        /**
		 * Type: Media
		 */
		$this->controls['video'] = [
			'tab'      => 'custom',
			'label'    => esc_html__( 'Video Media', 'bricks' ),
			'type'     => 'video',
            'description' => esc_html__('Select a video. If left empty, no video will display.', 'bricks'),
		];
        $this->controls['add_mobile_video'] = [
			'tab'      => 'content',
			'label'    => esc_html__( 'Add Mobile Version of Video?', 'bricks' ),
			'type'     => 'checkbox',
			'default'  => false,
		];
        $this->controls['video_mobile'] = [
			'tab'      => 'custom',
			'label'    => esc_html__( 'Video Media (Mobile)', 'bricks' ),
			'type'     => 'video',
            'description' => esc_html__('Select a video for mobile devices. If left empty, the default video will be used.', 'bricks'),
            'required' => [ 'add_mobile_video', '=', true ],
		];
        $this->controls['poster_image'] = [
			'tab'         => 'custom',
			'label'       => esc_html__( 'Poster Image', 'bricks' ),
			'type'        => 'text',
			'description' => esc_html__( 'Set for video SEO best practices via poster attribute on the video tag. It will be used as preview image.', 'bricks' ),
		];
        $this->controls['load_trigger'] = [
            'tab'   => 'custom',
            'label' => esc_html__('Load Trigger', 'bricks'),
            'type'  => 'select',
            'options' => [
                'targeted'  => esc_html__('Targeted Interaction', 'bricks'),
                'intersection'  => esc_html__('Intersection Observer', 'bricks'),
                'load' => esc_html__('Window Loaded', 'bricks'),
                'interaction'  => esc_html__('User Interaction', 'bricks'),
            ],
            'default' => 'intersection',
        ];
        $this->controls['targets'] = [
            'tab'   => 'custom',
            'label' => esc_html__('Targets', 'bricks'),
            'type'  => 'text',
            'placeholder' => '#top-nav-bar, #top-mega-menu-homes',
            'description' => esc_html__('Enter the CSS selectors (comma separated) of the elements that will trigger the video load when interacted with.', 'bricks'),
            'required' => [ 'load_trigger', '=', 'targeted' ],
        ];

        $this->controls['mobile_breakpoint'] = [
            'tab'   => 'responsive',
            'label' => esc_html__('Mobile Breakpoint', 'bricks'),
            'type'  => 'number',
            'placeholder' => '768',
            'description' => esc_html__('Viewport width in pixels where mobile view displays.', 'bricks'),
            'default' => 768,
            'required' => [ 'video_mobile', '!=', '' ],
        ];
    }

    public function render()
    {
        $settings = $this->settings;
        if (empty($settings['video'])) return;

        $video_url = ( ! empty( $settings['video']['url'] ) ) ? esc_url( $settings['video']['url'] ) : '';
        if (empty($video_url)) return;

        
        $add_mobile_video = $settings['add_mobile_video'] ?? false;
        $video_mobile_url = $add_mobile_video && ( ! empty( $settings['video_mobile']['url'] ) ) ? esc_url( $settings['video_mobile']['url'] ) : '';
        $poster_image = esc_url($settings['poster_image'] ?? '');
        $video_trigger = $settings['load_trigger'] ?? 'intersection';
        $video_targets = $settings['targets'] ?? '#brx-header';
        $id = $settings["id"] ?? "";

        if (! empty($id)) {
            $this->set_attribute('_root', 'id', $id);
        }
        // Set the video URLs as data attributes for lazy loading
        
        $this->set_attribute('_root', 'data-src', $video_url);
        if (! empty($video_mobile_url)) {
            $this->set_attribute('_root', 'data-src-mobile', $video_mobile_url);
        }
        if (! empty($poster_image)) {
            $this->set_attribute('_root', 'poster', $poster_image);
        }
        $this->set_attribute('_root', 'data-trigger', $video_trigger);
        if ($video_trigger == 'targeted') $this->set_attribute('_root', 'data-targets', $video_targets);
        $this->set_attribute('_root', 'aria-hidden', 'true');
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
            'lazy-load-bg-video-style',
            get_stylesheet_directory_uri() . '/elements/lazy-load-bg-video/lazy-load-bg-video.css',
            array(),
            mt_rand()
        );

        wp_enqueue_script(
            'lazy-load-bg-video-script',
            get_stylesheet_directory_uri() . '/elements/lazy-load-bg-video/lazy-load-bg-video.js',
            array(),
            mt_rand()
        );
    }
    
}
