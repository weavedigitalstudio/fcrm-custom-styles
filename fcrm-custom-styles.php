<?php
/**
 * Plugin Name:       FirehawkCRM Tributes - Custom Styles
 * Plugin URI:        https://github.com/weavedigitalstudio/fcrm-custom-styles/
 * Description:       Adds a settings page to easily style the FireHawkCRM Tributes plugin colours and buttons with your custom site colours.
 * Version:           0.1.3
 * Author:            Weave Digital Studio, Gareth Bissland
 * License:           MIT
 * License URI:       https://opensource.org/licenses/MIT
 * GitHub Plugin URI: weavedigitalstudio/fcrm-custom-styles
 * Primary Branch:    main
 * Requires at least: 6.0
 * Requires PHP:      7.2
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

// Define plugin constants for reusability
define('WEAVE_FCRM_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WEAVE_FCRM_PLUGIN_PATH', plugin_dir_path(__FILE__));

// Check if the FCRM Tributes plugin is active before proceeding
function weave_is_fcrm_tributes_active() {
	return in_array(plugin_basename('fcrm-tributes/fcrm-tributes.php'), apply_filters('active_plugins', get_option('active_plugins'))) || class_exists('Fcrm_Tributes');
}

// Create a settings page for the plugin if FCRM Tributes is active
function weave_fcrm_settings_page() {
	if (!weave_is_fcrm_tributes_active()) {
		return;
	}

	add_menu_page(
		'FCRM Style Settings',
		'FCRM Styles',
		'manage_options',
		'weave-fcrm-style-settings',
		'weave_fcrm_render_settings_page',
		'dashicons-art',
		100
	);
}
add_action('admin_menu', 'weave_fcrm_settings_page');

// Register settings and fields
function weave_fcrm_register_settings() {
	if (!weave_is_fcrm_tributes_active()) {
		return;
	}

	// Add settings section
	add_settings_section(
		'weave_fcrm_color_section',
		'Colour Settings',
		null,
		'weave-fcrm-style-settings'
	);

	// Define all settings
	$settings = array(
		'fcrm-link-color' => 'Link Colour',
		'fcrm-primary-color' => 'Primary Colour',
		'fcrm-secondary-color' => 'Secondary Colour',
		'fcrm-primary-button' => 'Primary Button Colour',
		'fcrm-primary-button-text' => 'Primary Button Text Colour',
		'fcrm-primary-button-hover' => 'Primary Button Hover Colour',
		'fcrm-primary-button-hover-text' => 'Primary Button Hover Text Colour',
		'fcrm-secondary-button' => 'Secondary Button Colour',
		'fcrm-secondary-button-text' => 'Secondary Button Text Colour',
		'fcrm-secondary-button-hover' => 'Secondary Button Hover Colour',
		'fcrm-secondary-button-hover-text' => 'Secondary Button Hover Text Colour',
		'fcrm-focus-shadow-color' => 'Focus Shadow Colour',
		'fcrm-focus-border-color' => 'Focus Border Colour',
		'fcrm-primary-shadow' => 'Primary Shadow Colour [box shading]'
	);

	// Register each setting and its field
	foreach ($settings as $setting_name => $setting_label) {
		register_setting(
			'weave_fcrm_style_settings_group',
			$setting_name,
			array(
				'type' => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default' => ''
			)
		);

		add_settings_field(
			$setting_name,
			$setting_label,
			'weave_fcrm_color_picker_field',
			'weave-fcrm-style-settings',
			'weave_fcrm_color_section',
			array('label_for' => $setting_name)
		);
	}
}
add_action('admin_init', 'weave_fcrm_register_settings');

// First, add this line to register the enqueue function:
add_action('admin_enqueue_scripts', 'weave_fcrm_enqueue_color_picker');

// Enqueue scripts and styles for the color picker
function weave_fcrm_enqueue_color_picker($hook_suffix) {
	if ('toplevel_page_weave-fcrm-style-settings' !== $hook_suffix) {
		return;
	}

	// Enqueue WordPress color picker
	wp_enqueue_style('wp-color-picker');
	wp_enqueue_script('wp-color-picker');
	
	// Add custom CSS for alpha slider
	wp_add_inline_style('wp-color-picker', '
		.alpha-color-picker-wrap .wp-picker-container .iris-picker {
			border-bottom: none;
		}
		
		.alpha-color-picker-wrap .wp-picker-container input[type=text].wp-color-picker {
			width: 195px;
		}
		
		.wp-picker-container .wp-picker-open ~ .wp-picker-holder .alpha-color-picker-container {
			display: block;
		}
		
		.alpha-color-picker-container {
			border: 1px solid #dfdfdf;
			border-top: none;
			display: none;
			background: #FFF;
			padding: 0 11px 10px;
			position: relative;
			width: 233px;
		}
		
		.alpha-color-picker-container .ui-widget-content,
		.alpha-color-picker-container .ui-widget-header,
		.alpha-color-picker-wrap .ui-state-focus {
			background: transparent;
			border: none;
		}
		
		.alpha-color-picker-wrap a.iris-square-value:focus {
			-webkit-box-shadow: none;
			box-shadow: none;
		}
		
		.alpha-color-picker-container .ui-slider {
			position: relative;
			z-index: 1;
			height: 24px;
			text-align: center;
			margin: 0 auto;
			width: 88%;
			width: calc( 100% - 28px );
		}
		
		.alpha-color-picker-container .ui-slider-handle,
		.alpha-color-picker-container .ui-widget-content .ui-state-default {
			color: #777;
			background: #FFF;
			text-shadow: 0 1px 0 #FFF;
			text-decoration: none;
			position: absolute;
			z-index: 2;
			box-shadow: 0 1px 2px rgba(0,0,0,0.2);
			border: 1px solid #aaa;
			-webkit-border-radius: 4px;
			-moz-border-radius: 4px;
			border-radius: 4px;
			margin-top: -2px;
			top: 0;
			height: 26px;
			width: 26px;
			cursor: ew-resize;
			font-size: 0;
			padding: 0;
			line-height: 27px;
			margin-left: -14px;
		}
		
		.alpha-color-picker-container .ui-slider-handle.show-opacity {
			font-size: 12px;
		}
		
		.alpha-color-picker-container .click-zone {
			width: 14px;
			height: 24px;
			display: block;
			position: absolute;
			left: 10px;
		}
		
		.alpha-color-picker-container .max-click-zone {
			right: 10px;
			left: auto;
		}
		
		.alpha-color-picker-container .transparency {
			height: 24px;
			width: 100%;
			background-color: #FFF;
			background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAIAAAHnlligAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAHJJREFUeNpi+P///4EDBxiAGMgCCCAGFB5AADGCRBgYDh48CCRZIJS9vT2QBAggFBkmBiSAogxFBiCAoHogAKIKAlBUYTELAiAmEtABEECk20G6BOmuIl0CIMBQ/IEMkO0myiSSraaaBhZcbkUOs0HuBwDplz5uFJ3Z4gAAAABJRU5ErkJggg==);
			box-shadow: 0 0 5px rgba(0,0,0,0.4) inset;
			-webkit-border-radius: 3px;
			-moz-border-radius: 3px;
			border-radius: 3px;
			padding: 0;
			margin-top: -24px;
		}
		
		@media screen and ( max-width: 782px ) {
			.wp-picker-container input[type=text].wp-color-picker {
				width: 145px;
			}
		}
	');

	// Enqueue alpha color picker
	wp_enqueue_script(
		'alpha-color-picker',
		WEAVE_FCRM_PLUGIN_URL . 'js/alpha-color-picker.js',
		array('wp-color-picker', 'jquery-ui-slider'),
		'1.0',
		true
	);

	// Initialize alpha color picker
	wp_add_inline_script(
		'alpha-color-picker',
		'jQuery(document).ready(function($) {
			$(".alpha-color-control").each(function() {
				var $input = $(this);
				var value = $input.val();
				var alpha = 100;
				
				// If value is rgba, get alpha value
				if (value && value.match(/rgba/)) {
					alpha = Math.floor(value.replace(/^.*,(.+)\)/, "$1") * 100);
				}
				
				// Initialize alpha color picker
				$input.alphaColorPicker({
					clear: function() {
						$input.val("").trigger("change");
					},
					change: function(event, ui) {
						setTimeout(function() {
							$input.trigger("change");
						}, 100);
					},
					defaultColor: false,
					showAlpha: true,
					alpha: alpha
				});
				
			});
		});'
	);
}

// Update the field function to include proper wrapper
function weave_fcrm_color_picker_field($args) {
	$option = get_option($args['label_for']);
	$default_color = '';
	
	printf(
		'<div class="alpha-color-picker-wrap">
			<input type="text" 
				id="%s" 
				name="%s" 
				value="%s" 
				class="alpha-color-control" 
				data-alpha="true" 
				data-show-opacity="true" 
				data-default-color="%s"
			/>
		</div>',
		esc_attr($args['label_for']),
		esc_attr($args['label_for']),
		esc_attr($option),
		esc_attr($default_color)
	);
}

// Render the settings page
function weave_fcrm_render_settings_page() {
	if (!current_user_can('manage_options')) {
		return;
	}
	?>
	<div class="wrap">
		<h1><?php echo esc_html(get_admin_page_title()); ?></h1>
		<p>Welcome to the FireHawkCRM Tributes custom styles settings page. Use the fields below to adjust the appearance of the tributes and buttons in the FireHawkCRM plugin.</p>
		
		<form method="post" action="options.php">
			<?php
			settings_fields('weave_fcrm_style_settings_group');
			do_settings_sections('weave-fcrm-style-settings');
			submit_button('Save Changes');
			?>
		</form>

		<form method="post">
			<input type="hidden" name="weave_fcrm_reset_options" value="true" />
			<?php submit_button('Reset Settings', 'delete', 'reset', false); ?>
		</form>

		<hr />
		<p style="font-size: 14px; margin-top: 20px;">
			Need help or want to report an issue? Check out our 
			<a href="https://github.com/weavedigitalstudio/fcrm-custom-styles" target="_blank" style="text-decoration: underline;">
				GitHub repository
			</a> 
			for support and documentation.
		</p>
	</div>
	<?php
}

// Handle resetting options
function weave_fcrm_handle_reset_options() {
	if (isset($_POST['weave_fcrm_reset_options']) && $_POST['weave_fcrm_reset_options'] === 'true') {
		weave_fcrm_reset_options();
		wp_redirect(add_query_arg('settings-reset', 'true', admin_url('admin.php?page=weave-fcrm-style-settings')));
		exit;
	}
}
add_action('admin_init', 'weave_fcrm_handle_reset_options');

// Display reset confirmation notice
function weave_fcrm_display_reset_notice() {
	if (isset($_GET['settings-reset']) && $_GET['settings-reset'] === 'true') {
		echo '<div class="notice notice-success is-dismissible"><p>All FireHawkCRM Style settings have been reset to defaults.</p></div>';
	}
}
add_action('admin_notices', 'weave_fcrm_display_reset_notice');

// Reset all options to default
function weave_fcrm_reset_options() {
	$options = array(
		'fcrm-link-color',
		'fcrm-primary-button',
		'fcrm-primary-button-text',
		'fcrm-primary-button-hover',
		'fcrm-primary-button-hover-text',
		'fcrm-secondary-button',
		'fcrm-secondary-button-text',
		'fcrm-secondary-button-hover',
		'fcrm-secondary-button-hover-text',
		'fcrm-focus-shadow-color',
		'fcrm-focus-border-color',
		'fcrm-primary-color',
		'fcrm-secondary-color',
		'fcrm-primary-shadow'
	);

	foreach ($options as $option) {
		delete_option($option);
	}
}

// Enqueue custom stylesheet with dynamic CSS
function weave_firehawk_crm_tributes_styles() {
	if (!weave_is_fcrm_tributes_active()) {
		return;
	}

	wp_enqueue_style(
		'weave-firehawk-crm-tributes-styles',
		WEAVE_FCRM_PLUGIN_URL . 'css/weave-fcrm-tributes.css',
		array(),
		'1.0',
		'all'
	);

	$custom_css = weave_generate_dynamic_css();
	wp_add_inline_style('weave-firehawk-crm-tributes-styles', $custom_css);
}
add_action('wp_enqueue_scripts', 'weave_firehawk_crm_tributes_styles', 99);

// Generate dynamic CSS
function weave_generate_dynamic_css() {
	$custom_css = ":root {";
	$variables = array(
		'fcrm-link-color',
		'fcrm-primary-color',
		'fcrm-secondary-color',
		'fcrm-primary-button',
		'fcrm-primary-button-text',
		'fcrm-primary-button-hover',
		'fcrm-primary-button-hover-text',
		'fcrm-secondary-button',
		'fcrm-secondary-button-text',
		'fcrm-secondary-button-hover',
		'fcrm-secondary-button-hover-text',
		'fcrm-focus-shadow-color',
		'fcrm-focus-border-color',
		'fcrm-primary-shadow'
	);

	foreach ($variables as $variable) {
		$value = get_option($variable, '');
		if (!empty($value)) {
			$custom_css .= "--$variable: $value;";
		}
	}

	$custom_css .= "}";
	return $custom_css;
}