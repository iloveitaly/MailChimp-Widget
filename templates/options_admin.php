<div class="wrap">
	<h2><?php echo __('MailChimp Widget Settings', 'mailchimp-widget'); ?></h2>
<?php
if(!function_exists('curl_init')) {
	echo __('You need to have the PHP Client URL library enabled for this plugin to work. You can find more information about installing it <a href="http://php.net/manual/en/book.curl.php">here</a>.');
} else {
?>
	<p><?php echo __('Enter a valid MailChimp API key here to get started. Once you\'ve done that, you can use the MailChimp Widget from the Widgets menu. You will need to have at least MailChimp list set up before the using the widget.', 'mailchimp-widget') ?> 				
	</p>
	<form action="options.php" method="POST">
		<?php settings_fields('ns_mc_options'); ?>
		<?php do_settings_sections('ns_mc_options'); ?>

		<h3>MailChimp API Key</h3>
		<input class="regular-text" name="ns_mc_api_key" type="text" value="<?php echo get_option('ns_mc_api_key'); ?>" />

		<?php submit_button(); ?>
	</form>
<?php	} ?>
</div>