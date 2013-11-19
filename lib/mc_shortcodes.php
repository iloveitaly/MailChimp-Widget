<?php

add_action('init', 'mc_register_shortcodes');
function mc_register_shortcodes(){
   add_shortcode('mc-widget', 'mc_shortcode_widget');
}

function mc_shortcode_widget($atts) {
	// http://digwp.com/2010/04/call-widget-with-shortcode/
	// http://wp.smashingmagazine.com/2012/05/01/wordpress-shortcodes-complete-guide/

	$widget_id = $atts['id'];

	$widget_options = array_merge(array(
		'before_widget' => '<div class="col-md-6">',
		'after_widget' => '</div>',
		'before_title' => '<h3>',
		'after_title' => '</h3>',
		'form_id' => 'shortcode-mailchimp-' . $widget_id
	), $atts);

	$instance = new NS_Widget_MailChimp();
	$instance->_set($widget_id);

	$all_widget_options = get_option("widget_ns_widget_mailchimp");
	$all_widget_options[$widget_id];

	ob_start();
	$instance->widget($widget_options, $all_widget_options[$widget_id]);
	$output = ob_get_contents();
	ob_end_clean();

	return $output;
}
