<?php
/**
 * Plugin Name:       FirehawkCRM Tributes - Custom Styles
 * Plugin URI:        https://github.com/weavedigitalstudio/fcrm-custom-styles/
 * Description:       Adds a settings page to easily style the FireHawkCRM Tributes plugin colours and buttons with your custom styles.
 * Version:           0.1.0
 * Author:            Weave Digital Studio, Gareth Bissland
 * License:           MIT
 * License URI:       https://opensource.org/licenses/MIT
 * GitHub Plugin URI: weavedigitalstudio/fcrm-custom-styles
 * Primary Branch:    main
 * Requires at least: 6.0
 * Requires PHP:      7.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Define plugin constants for reusability
define( 'WEAVE_FCRM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'WEAVE_FCRM_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

// Check if the FCRM Tributes plugin is active before proceeding
function weave_is_fcrm_tributes_active() {
	return in_array( plugin_basename( 'fcrm-tributes/fcrm-tributes.php' ), apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) || class_exists( 'Fcrm_Tributes' );
}

// Enqueue custom stylesheet with dynamic CSS if FCRM Tributes is active
function weave_firehawk_crm_tributes_styles() {
	if ( ! weave_is_fcrm_tributes_active() ) {
		return;
	}

	wp_enqueue_style(
		'weave-firehawk-crm-tributes-styles',
		WEAVE_FCRM_PLUGIN_URL . 'css/weave-fcrm-tributes.css',
		array(),
		'1.0',
		'all'
	);

	// Enqueue the custom dynamic CSS
	$custom_css = weave_generate_dynamic_css();
	wp_add_inline_style( 'weave-firehawk-crm-tributes-styles', $custom_css );
}
add_action( 'wp_enqueue_scripts', 'weave_firehawk_crm_tributes_styles', 99 );

// Create a settings page for the plugin if FCRM Tributes is active
function weave_fcrm_settings_page() {
	if ( ! weave_is_fcrm_tributes_active() ) {
		return;
	}

	add_menu_page(
		'FCRM Style Settings',
		'FCRM Styles',
		'manage_options', // Capability check
		'weave-fcrm-style-settings',
		'weave_fcrm_render_settings_page',
		'dashicons-art', // Icon for the menu item
		100
	);
}
add_action( 'admin_menu', 'weave_fcrm_settings_page' );

// Render the settings page content
function weave_fcrm_render_settings_page() {
	?>
	<div class="wrap">
		<h1>FireHawkCRM Tributes Style Settings</h1>
		<!-- General description text -->
		<p>Welcome to the FireHawkCRM Tributes custom styles settings page. Use the fields below to adjust the appearance of the tributes and buttons in the FireHawkCRM plugin.</p>
		<form method="post" action="options.php">
			<?php
				settings_fields( 'weave_fcrm_style_settings_group' );
				do_settings_sections( 'weave-fcrm-style-settings' );
				submit_button();
			?>
		</form>
		<!-- Add a Reset Button -->
		<form method="post">
			<input type="hidden" name="weave_fcrm_reset_options" value="true" />
			<?php submit_button( 'Reset Settings', 'delete', 'reset', false ); ?>
		</form>
		<!-- Additional information and support link at the bottom -->
		<hr />
		<p style="font-size: 14px; margin-top: 20px;">Need help or want to report an issue? Check out our <a href="https://github.com/weavedigitalstudio/fcrm-custom-styles" target="_blank" style="text-decoration: underline;">GitHub repository</a> for support and documentation.</p>
	</div>
	<?php
}

// Register settings, sections, and fields only if FCRM Tributes is active
function weave_fcrm_register_settings() {
	if ( ! weave_is_fcrm_tributes_active() ) {
		return;
	}

	// Register settings using dashes in both the database and CSS variable names
	register_setting( 'weave_fcrm_style_settings_group', 'fcrm-link-color' );
	register_setting( 'weave_fcrm_style_settings_group', 'fcrm-primary-button' );
	register_setting( 'weave_fcrm_style_settings_group', 'fcrm-primary-button-text' );
	register_setting( 'weave_fcrm_style_settings_group', 'fcrm-primary-button-hover' );
	register_setting( 'weave_fcrm_style_settings_group', 'fcrm-primary-button-hover-text' );
	register_setting( 'weave_fcrm_style_settings_group', 'fcrm-secondary-button' );
	register_setting( 'weave_fcrm_style_settings_group', 'fcrm-secondary-button-text' );
	register_setting( 'weave_fcrm_style_settings_group', 'fcrm-secondary-button-hover' );
	register_setting( 'weave_fcrm_style_settings_group', 'fcrm-secondary-button-hover-text' );
	register_setting( 'weave_fcrm_style_settings_group', 'fcrm-focus-shadow-color' );
	register_setting( 'weave_fcrm_style_settings_group', 'fcrm-focus-border-color' );
	register_setting( 'weave_fcrm_style_settings_group', 'fcrm-primary-color' );
	register_setting( 'weave_fcrm_style_settings_group', 'fcrm-secondary-color' );
	register_setting( 'weave_fcrm_style_settings_group', 'fcrm-primary-shadow' );

	// Add the settings section
	add_settings_section( 'weave_fcrm_color_section', 'Colour Settings', null, 'weave-fcrm-style-settings' );

	add_settings_field( 'fcrm-link-color', 'Link Colour', 'weave_fcrm_color_picker_field', 'weave-fcrm-style-settings', 'weave_fcrm_color_section', array( 'label_for' => 'fcrm-link-color' ) );
	add_settings_field( 'fcrm-primary-color', 'Primary Colour', 'weave_fcrm_color_picker_field', 'weave-fcrm-style-settings', 'weave_fcrm_color_section', array( 'label_for' => 'fcrm-primary-color' ) );
	add_settings_field( 'fcrm-secondary-color', 'Secondary Colour', 'weave_fcrm_color_picker_field', 'weave-fcrm-style-settings', 'weave_fcrm_color_section', array( 'label_for' => 'fcrm-secondary-color' ) );
	add_settings_field( 'fcrm-primary-button', 'Primary Button Colour', 'weave_fcrm_color_picker_field', 'weave-fcrm-style-settings', 'weave_fcrm_color_section', array( 'label_for' => 'fcrm-primary-button' ) );
	add_settings_field( 'fcrm-primary-button-text', 'Primary Button Text Colour', 'weave_fcrm_color_picker_field', 'weave-fcrm-style-settings', 'weave_fcrm_color_section', array( 'label_for' => 'fcrm-primary-button-text' ) );
	add_settings_field( 'fcrm-primary-button-hover', 'Primary Button Hover Colour', 'weave_fcrm_color_picker_field', 'weave-fcrm-style-settings', 'weave_fcrm_color_section', array( 'label_for' => 'fcrm-primary-button-hover' ) );
	add_settings_field( 'fcrm-primary-button-hover-text', 'Primary Button Hover Text Colour', 'weave_fcrm_color_picker_field', 'weave-fcrm-style-settings', 'weave_fcrm_color_section', array( 'label_for' => 'fcrm-primary-button-hover-text' ) );
	add_settings_field( 'fcrm-secondary-button', 'Secondary Button Colour', 'weave_fcrm_color_picker_field', 'weave-fcrm-style-settings', 'weave_fcrm_color_section', array( 'label_for' => 'fcrm-secondary-button' ) );
	add_settings_field( 'fcrm-secondary-button-text', 'Secondary Button Text Colour', 'weave_fcrm_color_picker_field', 'weave-fcrm-style-settings', 'weave_fcrm_color_section', array( 'label_for' => 'fcrm-secondary-button-text' ) );
	add_settings_field( 'fcrm-secondary-button-hover', 'Secondary Button Hover Colour', 'weave_fcrm_color_picker_field', 'weave-fcrm-style-settings', 'weave_fcrm_color_section', array( 'label_for' => 'fcrm-secondary-button-hover' ) );
	add_settings_field( 'fcrm-secondary-button-hover-text', 'Secondary Button Hover Text Colour', 'weave_fcrm_color_picker_field', 'weave-fcrm-style-settings', 'weave_fcrm_color_section', array( 'label_for' => 'fcrm-secondary-button-hover-text' ) );
	add_settings_field( 'fcrm-focus-shadow-color', 'Focus Shadow Colour', 'weave_fcrm_color_picker_field', 'weave-fcrm-style-settings', 'weave_fcrm_color_section', array( 'label_for' => 'fcrm-focus-shadow-color' ) );
	add_settings_field( 'fcrm-focus-border-color', 'Focus Border Colour', 'weave_fcrm_color_picker_field', 'weave-fcrm-style-settings', 'weave_fcrm_color_section', array( 'label_for' => 'fcrm-focus-border-color' ) );
	add_settings_field( 'fcrm-primary-shadow', 'Primary Shadow Colour [box shading]', 'weave_fcrm_color_picker_field', 'weave-fcrm-style-settings', 'weave_fcrm_color_section', array( 'label_for' => 'fcrm-primary-shadow' ) );
}
add_action( 'admin_init', 'weave_fcrm_register_settings' );
	
function weave_fcrm_enqueue_color_picker( $hook_suffix ) {
	// Check if we are on the desired settings page
	if ( 'toplevel_page_weave-fcrm-style-settings' !== $hook_suffix ) {
		return;
	}

	// Enqueue the default WordPress color picker script and style
	wp_enqueue_style( 'wp-color-picker' );

	// Register and enqueue the wp-color-picker-alpha script
	wp_register_script(
		'wp-color-picker-alpha',
		plugin_dir_url( __FILE__ ) . 'js/wp-color-picker-alpha.js', // Correct path to the alpha script
		array( 'wp-color-picker' ), // Make sure wp-color-picker is a dependency
		'3.0.0',
		true
	);

	// Add inline script to initialize the color picker with alpha options and force reinitialization
	wp_add_inline_script(
		'wp-color-picker-alpha',
		'jQuery(document).ready(function($) {
			$(".weave-color-picker").wpColorPicker({
				palettes: true, // Show predefined color palettes
				defaultColor: "", // Default value if none is set
				showInput: true,  // Show input field for manual input
				allowEmpty: true, // Allow empty values
				alpha: true,      // Enable alpha channel (transparency) support
				mode: "rgba",     // Set the mode to RGBA to enable the alpha slider
				clear: function() {
					console.log("Color cleared");
					$(this).val("");
				}
			});
		});'
	);

	// Enqueue the wp-color-picker-alpha script
	wp_enqueue_script( 'wp-color-picker-alpha' );
}
add_action( 'admin_enqueue_scripts', 'weave_fcrm_enqueue_color_picker' );

function weave_fcrm_color_picker_field( $args ) {
	$option = get_option( $args['label_for'] );

	// Allow RGBA values to be set or pasted, or use default hex values
	echo '<input type="text" id="' . esc_attr( $args['label_for'] ) . '" name="' . esc_attr( $args['label_for'] ) . '" value="' . esc_attr( $option ) . '" class="weave-color-picker" />';
}

// Generate dynamic CSS based on settings
function weave_generate_dynamic_css() {
	$custom_css = ":root {";

	// Define the CSS variables and use the same names to retrieve them
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

	// Loop through each variable and add it to the CSS only if it has a non-empty value
	foreach ( $variables as $variable ) {
		$value = get_option( $variable, '' ); // Use the exact same option names in the database
		if ( ! empty( $value ) ) {
			$custom_css .= "--$variable: $value;"; // Add semicolon at the end of each variable definition
		}
	}

	$custom_css .= "}";

	return $custom_css;
}

// Reset colours in the database
add_action( 'admin_init', 'weave_fcrm_handle_reset_options' );

function weave_fcrm_handle_reset_options() {
	// Check if the reset form is submitted
	if ( isset( $_POST['weave_fcrm_reset_options'] ) && $_POST['weave_fcrm_reset_options'] === 'true' ) {
		// Call the reset function
		weave_fcrm_reset_options();

		// Redirect to prevent re-submission on page refresh
		wp_redirect( add_query_arg( 'settings-reset', 'true', admin_url( 'admin.php?page=weave-fcrm-style-settings' ) ) );
		exit;
	}
}

// Display a reset confirmation notice
add_action( 'admin_notices', 'weave_fcrm_display_reset_notice' );

function weave_fcrm_display_reset_notice() {
	if ( isset( $_GET['settings-reset'] ) && $_GET['settings-reset'] === 'true' ) {
		echo '<div class="notice notice-success is-dismissible"><p>All FireHawkCRM Style settings have been reset to defaults.</p></div>';
	}
}

// Function to reset all custom style options to default (delete them from the database)
function weave_fcrm_reset_options() {
	// List of all options to delete (using dashes)
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

	// Loop through each option and delete it
	foreach ( $options as $option ) {
		delete_option( $option );
	}
}