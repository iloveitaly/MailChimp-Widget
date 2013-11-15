<?php

class NS_Widget_MailChimp extends WP_Widget {
	private $default_failure_message;
	private $default_signup_text;
	private $default_success_message;
	private $default_already_subscribed_message;
	private $default_title;
	private $successful_signup = false;
	private $subscribe_errors;
	private $ns_mc_plugin;
	
	public function NS_Widget_MailChimp() {
		$this->default_failure_message = __('There was a problem processing your submission.');
		$this->default_signup_text = __('Join now!');
		$this->default_success_message = __('Thank you for joining our mailing list. Please check your email for a confirmation link.');
		$this->default_already_subscribed_message = __("You are already subscribed. Thank you for joining our list!");
		$this->default_title = __('Sign up for our mailing list.');

		$widget_options = array('classname' => 'widget_ns_mailchimp', 'description' => __( "Displays a sign-up form for a MailChimp mailing list.", 'mailchimp-widget'));
		$this->WP_Widget('ns_widget_mailchimp', __('MailChimp List Signup', 'mailchimp-widget'), $widget_options);
		$this->ns_mc_plugin = NS_MC_Plugin::get_instance();

		add_action('init', array(&$this, 'add_scripts'));
		add_action('parse_request', array(&$this, 'process_submission'));
	}
	
	public function add_scripts() {
		wp_enqueue_style('ns-mc-widget', MAILCHIMP_PLUGIN_URL . 'bower_components/jquery.loadmask.spin/jquery.loadmask.spin.css' );

		wp_enqueue_script('ns-mc-widget-spin', MAILCHIMP_PLUGIN_URL . 'bower_components/spin.js/dist/spin.min.js', array('jquery'), false);
		wp_enqueue_script('ns-mc-widget-loadmask', MAILCHIMP_PLUGIN_URL . 'bower_components/jquery.loadmask.spin/jquery.loadmask.spin.js', array('jquery', 'ns-mc-widget-spin'), false);
		wp_enqueue_script('ns-mc-widget', MAILCHIMP_PLUGIN_URL . 'js/mailchimp-widget.js', array('jquery'), false);
	}
	
	public function form($instance) {
		$mcapi = $this->ns_mc_plugin->get_mcapi();

		if ($mcapi == false) {
			echo $this->ns_mc_plugin->get_admin_notices();
		} else {
			$this->lists = $mcapi->lists();
			$defaults = array(
				'failure_message' => $this->default_failure_message,
				'title' => $this->default_title,
				'description' => '',
				'signup_text' => $this->default_signup_text,
				'success_message' => $this->default_success_message,
				'already_subscribed_message' => $this->default_already_subscribed_message,
				'group_subscriptions' => '',
				'group_name' => '',
				'collect_first' => false,
				'collect_last' => false,
				'old_markup' => false
			);

			// TODO: don't like extract; too much magic, manually define variables in the future
			$vars = wp_parse_args($instance, $defaults);
			extract($vars);

			// TODO this should all be in a template; I really don't like the WP convention of miking plugin logic + HTML together
			?>
					<h3><?php echo  __('General Settings', 'mailchimp-widget'); ?></h3>
					<p>
						<label for="<?php echo $this->get_field_id('title'); ?>"><?php echo  __('Title :', 'mailchimp-widget'); ?></label>
						<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
					</p>
					<p>
						<label for="<?php echo $this->get_field_id('description'); ?>"><?php echo __('Description :', 'mailchimp-widget'); ?></label>
						<textarea class="widefat" id="<?php echo $this->get_field_id('description'); ?>" name="<?php echo $this->get_field_name('description'); ?>"><?php echo $description; ?></textarea>
					</p>

					<p>
						<label for="<?php echo $this->get_field_id('current_mailing_list'); ?>"><?php echo __('Select a Mailing List :', 'mailchimp-widget'); ?></label>
						<select class="widefat" id="<?php echo $this->get_field_id('current_mailing_list');?>" name="<?php echo $this->get_field_name('current_mailing_list'); ?>">
			<?php	
			foreach ($this->lists['data'] as $key => $value) {
				$selected = (isset($current_mailing_list) && $current_mailing_list == $value['id']) ? ' selected="selected" ' : '';
				?>	
						<option <?php echo $selected; ?>value="<?php echo $value['id']; ?>"><?php echo __($value['name'], 'mailchimp-widget'); ?></option>
				<?php
			}
			?>
						</select>
					</p>
					<p><?php echo  __('This is the list your users will be signing up for in your sidebar.', 'mailchimp-widget'); ?></p>
					<p>
						<label for="<?php echo $this->get_field_id('signup_text'); ?>"><?php echo __('Sign Up Button Text :', 'mailchimp-widget'); ?></label>
						<input class="widefat" id="<?php echo $this->get_field_id('signup_text'); ?>" name="<?php echo $this->get_field_name('signup_text'); ?>" value="<?php echo $signup_text; ?>" />
					</p>
					<h3><?php echo __('Personal Information', 'mailchimp-widget'); ?></h3>
					<p><?php echo __("These fields won't (and shouldn't) be required. Should the widget form collect users' first and last names?", 'mailchimp-widget'); ?></p>
					<p>
						<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('collect_first'); ?>" name="<?php echo $this->get_field_name('collect_first'); ?>" <?php echo  checked($collect_first, true, false); ?> />
						<label for="<?php echo $this->get_field_id('collect_first'); ?>"><?php echo  __('Collect first name.', 'mailchimp-widget'); ?></label>
						<br />
						<input type="checkbox" class="checkbox" id="<?php echo  $this->get_field_id('collect_last'); ?>" name="<?php echo $this->get_field_name('collect_last'); ?>" <?php echo checked($collect_last, true, false); ?> />
						<label><?php echo __('Collect last name.', 'mailchimp-widget'); ?></label>
					</p>
					<h3><?php echo __('Notifications', 'mailchimp-widget'); ?></h3>
					<p><?php echo  __('Use these fields to customize what your visitors see after they submit the form', 'mailchimp-widget'); ?></p>
					<p>
						<label for="<?php echo $this->get_field_id('success_message'); ?>"><?php echo __('Success :', 'mailchimp-widget'); ?></label>
						<textarea class="widefat" id="<?php echo $this->get_field_id('success_message'); ?>" name="<?php echo $this->get_field_name('success_message'); ?>"><?php echo $success_message; ?></textarea>
					</p>
					<p>
						<label for="<?php echo $this->get_field_id('already_subscribed_message'); ?>"><?php echo __('Already Subscribed :', 'mailchimp-widget'); ?></label>
						<textarea class="widefat" id="<?php echo $this->get_field_id('already_subscribed_message'); ?>" name="<?php echo $this->get_field_name('already_subscribed_message'); ?>"><?php echo $already_subscribed_message; ?></textarea>
					</p>
					<p>
						<label for="<?php echo $this->get_field_id('failure_message'); ?>"><?php echo __('Failure :', 'mailchimp-widget'); ?></label>
						<textarea class="widefat" id="<?php echo $this->get_field_id('failure_message'); ?>" name="<?php echo $this->get_field_name('failure_message'); ?>"><?php echo $failure_message; ?></textarea>
					</p>
					<p>
						<label for="<?php echo $this->get_field_id('group_name'); ?>">Group Name</label>
						<textarea class="widefat" id="<?php echo $this->get_field_id('group_name'); ?>" name="<?php echo $this->get_field_name('group_name'); ?>"><?php echo $group_name; ?></textarea>
					</p>
					<p>
						<label for="<?php echo $this->get_field_id('group_subscriptions'); ?>">Group Subscriptions</label>
						<textarea class="widefat" id="<?php echo $this->get_field_id('group_subscriptions'); ?>" name="<?php echo $this->get_field_name('group_subscriptions'); ?>"><?php echo $group_subscriptions; ?></textarea>
					</p>
					<?php

					if(isset($current_mailing_list)) {
						echo "<h3>Groups</h3>";
						$group_listing = $mcapi->listInterestGroupings($current_mailing_list);

						foreach($group_listing as $key => $group_info) {
							echo "<b>".$group_info["name"]."</b><br/>";
							foreach($group_info["groups"] as $group_interests_info) {
								echo $group_interests_info["name"]."<br/>";
							}
							echo "<br/>";
						}
					}

					if(isset($this->number)) {
						echo "<h3>Shortcode</h3>";
						echo "<code>[mc-widget id=\"{$this->number}\" learn_more=\"/the-path\"]</code><br/><br/>";
					}
		}
	}
	
	public function process_submission() {
		$merge_vars = array();

		if(!empty($_REQUEST['ns_mc_number'])) {
			$this->_set(intval($_REQUEST['ns_mc_number']));
		}

		// handle optional first & last names

		if(!empty($_REQUEST[$this->id_base . '_first_name']) && is_string($_REQUEST[$this->id_base . '_first_name'])) {
			$merge_vars['FNAME'] = strip_tags($_REQUEST[$this->id_base . '_first_name']);
		}
		
		if(!empty($_REQUEST[$this->id_base . '_last_name']) && is_string($_REQUEST[$this->id_base . '_last_name'])) {
			$merge_vars['LNAME'] = strip_tags($_REQUEST[$this->id_base . '_last_name']);
		}

		// handle group subscriptions

		$group_subscriptions = $this->get_option('group_subscriptions');
		$group_name = $this->get_option('group_name');

		if(!empty($group_subscriptions)) {
			$merge_vars['GROUPINGS'] = array(
				"0" => array(
					'name' => $group_name,
					'groups' => $group_subscriptions
				)
			);
		}

		if (isset($_GET[$this->id_base . '_email'])) {
			header("Content-Type: application/json");
			
			// assume the worst.
			$response = '';
			$result = array(
				'success' => false,
				'error' => $this->get_option('failure_message')
			);
			
			if (!is_email($_GET[$this->id_base . '_email'])) { //Use WordPress's built-in is_email function to validate input.
				$response = json_encode($result); //If it's not a valid email address, just encode the defaults.
			} else {
				$mcapi = $this->ns_mc_plugin->get_mcapi();

				if($this->ns_mc_plugin == false) {
					$response = json_encode($result);
				} else {
					$subscribed = $mcapi->listSubscribeOrListUpdateMember($this->get_option('current_mailing_list'), $_GET[$this->id_base . '_email'], $merge_vars);
				
					if ($subscribed == false) {
						$result['mc_errorcode'] = $mcapi->errorCode;
						$response = json_encode($result);
					} else {
						$result['success'] = true;
						$result['error'] = '';

						if($subscribed === 2) {
							$result['success_message'] = $this->get_option('already_subscribed_message');
						} else {
							$result['success_message'] = $this->get_option('success_message');
						}

						$response = json_encode($result);
					}
				}
			}
			
			exit($response);
			
		} elseif (isset($_POST[$this->id_base . '_email'])) {
			$this->subscribe_errors = '<div class="error">'  . $this->get_option('failure_message') .  '</div>';

			if (!is_email($_POST[$this->id_base . '_email'])) {
				return false;
			}
			
			$mcapi = $this->ns_mc_plugin->get_mcapi();
			
			if($mcapi == false) {
				return false;
			}
			
			$subscribed = $mcapi->listSubscribeOrListUpdateMember($this->get_option('current_mailing_list'), $_POST[$this->id_base . '_email'], $merge_vars);
			
			if ($subscribed == false) {
				return false;
			} else {
				$this->subscribe_errors = '';
				
				setcookie($this->id_base . '-' . $this->number, $this->hash_mailing_list_id(), time() + 31556926);
				
				$this->successful_signup = true;
				
				if($subscribed === 2) {
					$this->signup_success_message = '<p>' . $this->get_option('already_subscribed_message') . '</p>';
				} else {
					$this->signup_success_message = '<p>' . $this->get_option('success_message').'</p>';
				}
				
				return true;
			}	
		}
	}
	
	public function update($new_instance, $old_instance) {
		$instance = $old_instance;

		$instance['collect_first'] = !empty($new_instance['collect_first']);
		$instance['collect_last'] = !empty($new_instance['collect_last']);
		$instance['current_mailing_list'] = esc_attr($new_instance['current_mailing_list']);
		$instance['failure_message'] = esc_attr($new_instance['failure_message']);
		$instance['signup_text'] = esc_attr($new_instance['signup_text']);
		$instance['success_message'] = esc_attr($new_instance['success_message']);
		$instance['already_subscribed_message'] = esc_attr($new_instance['already_subscribed_message']);
		$instance['title'] = esc_attr($new_instance['title']);
		$instance['description'] = esc_attr($new_instance['description']);
		$instance['group_name'] = esc_attr($new_instance['group_name']);
		$instance['group_subscriptions'] = esc_attr($new_instance['group_subscriptions']);

		return $instance;
	}
	
	public function widget($args, $instance) {
		extract($args);

		// we optionally define these for the shortcode
		if(!isset($form_id)) $form_id = $this->id_base . '_form-' . $this->number;
		if(!isset($description)) $description = $instance['description'];
		if(!isset($title)) $title = $instance['title'];

		if($this->ns_mc_plugin->get_mcapi() == false) {
			return 0;
		}

		echo $before_widget;

		if(!empty($title)) echo $before_title . $title . $after_title;
		
		if($this->successful_signup) {
			echo $this->signup_success_message;
		} else {
			// allow the user to customize the template without forking the plugin
			if($overridden_template = locate_template( 'templates/mc-widget.php')) {
				include $overridden_template;
			} else {
				include MAILCHIMP_WIDGET_PATH . 'templates/widget_template.php';
			}
		}

		echo $after_widget;
	}

	public function get_option($key) {
		$options = get_option($this->option_name);
		return $options[$this->number][$key];
	}
	
	private function hash_mailing_list_id () {
		$options = get_option($this->option_name);
		$hash = md5($options[$this->number]['current_mailing_list']);
		return $hash;
	}
}

?>
