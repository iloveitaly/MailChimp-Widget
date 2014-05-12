<?php

class NS_MC_Plugin {
	private $options;
	private static $instance;
	private static $mcapi;
	private static $name = 'NS_MC_Plugin';
	public static $prefix = 'ns_mc';
	private static $public_option = 'no';
	private static $textdomain = 'mailchimp-widget';
	
	private function __construct () {
		self::load_text_domain();
		register_activation_hook(__FILE__, array(&$this, 'set_up_options'));

		// Set up the settings.
		add_action('admin_init', array(&$this, 'register_settings'));

		// Set up the administration page.
		add_action('admin_menu', array(&$this, 'set_up_admin_page'));

		// Fetch the options, and, if they haven't been set up yet, display a notice to the user.
		$this->get_options();

		// if the API key isn't set, notify the user
		if($this->get_api_key() === false) {
			add_action('admin_notices', array(&$this, 'admin_notices'));
		}

		// Add our widget when widgets get intialized
		// TODO not sure why create_function is being used here; something to cleanup in the future
		add_action('widgets_init', create_function('', 'return register_widget("NS_Widget_MailChimp");'));
	}

	public static function get_instance () {
		if(empty(self::$instance)) {
			self::$instance = new self::$name;
		}

		return self::$instance;
	}
	
	public function admin_notices () {
		global $blog_id;

		// TODO this is messy; I bet there is a WP API to link to the options page
		echo '<div class="error fade"><p>' .
				 __('You\'ll need to set up the MailChimp signup widget plugin options before using it. ', 'mailchimp-widget') . __('You can make your changes', 'mailchimp-widget') . ' <a href="' . get_admin_url($blog_id) . 'options-general.php?page=mailchimp-widget/lib/ns_mc_plugin.class.php">' . __('here', 'mailchimp-widget') . '.</a>' .
				 '</p></div>';
	}

	public function admin_page () {
		load_template(MAILCHIMP_WIDGET_PATH . '/templates/options_admin.php');
	}
	
	public function get_mcapi () {
		$api_key = $this->get_api_key();

		if (false == $api_key) {
			return false;
		} else {
			if (empty(self::$mcapi)) {
				self::$mcapi = new MCAPI($api_key);
			}
			return self::$mcapi;
		}
	}
	
	public function get_options () {
		$this->options = get_option(self::$prefix . '_options');
		return $this->options;
	}
	
	public function load_text_domain () {
		load_plugin_textdomain(self::$textdomain, null, str_replace('lib', 'languages', dirname(plugin_basename(__FILE__))));
	}
	
	public function register_settings () {
		register_setting( self::$prefix . '_options', self::$prefix . '_options', array($this, 'validate_api_key'));
		register_setting( self::$prefix . '_options', 'ns_mc_api_key' );
	}
	
	public function set_up_admin_page () {
		add_options_page('MailChimp Widget Options', 'MailChimp Widget', 'manage_options', __FILE__, array(&$this, 'admin_page'));
	}

	public function set_up_options () {
		add_option(self::$prefix . '_options', '', '', self::$public_option);
	}
	
	public function validate_api_key ($api_key) {
		// TODO: Add API validation logic.
		return $api_key;
	}
	
	private function get_api_key() {
		$api_key = get_option('ns_mc_api_key');

		if(!empty($api_key)) {
			return $api_key;
		} else {
			return false;
		}
	}
	
	private function update_options($options_values) {
		$old_options_values = get_option(self::$prefix . '_options');
		$new_options_values = wp_parse_args($options_values, $old_options_values);
		update_option(self::$prefix .'_options', $new_options_values);
		$this->get_options();
	}
}
?>
