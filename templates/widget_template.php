<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" id="<?php echo $this->id_base . '_form-' . $this->number; ?>" method="post">
	<?php echo $this->subscribe_errors; ?>
	<?php	if ($instance['collect_first']): ?>
	<input placeholder="First Name" class="input-small" type="text" name="<?php echo $this->id_base . '_first_name'; ?>" />
	<?php endif; ?>
	<?php if ($instance['collect_last']): ?>
	<input placeholder="Last Name" class="input-small" type="text" name="<?php echo $this->id_base . '_last_name'; ?>" /></label>
	<?php	endif; ?>
	<br/>
	<input type="hidden" name="ns_mc_number" value="<?php echo $this->number; ?>" />
	<input placeholder="Email Address" id="<?php echo $this->id_base; ?>-email-<?php echo $this->number; ?>" type="text" name="<?php echo $this->id_base; ?>_email" /><br/>
	<input class="btn btn-success" type="submit" name="<?php echo __($instance['signup_text'], 'mailchimp-widget'); ?>" value="<?php echo __($instance['signup_text'], 'mailchimp-widget'); ?>" />
</form>
<script>jQuery('#<?php echo $this->id_base; ?>_form-<?php echo $this->number; ?>').ns_mc_widget({"url" : "<?php echo $_SERVER['PHP_SELF']; ?>", "cookie_id" : "<?php echo $this->id_base; ?>-<?php echo $this->number; ?>", "cookie_value" : "<?php echo $this->hash_mailing_list_id(); ?>", "loader_graphic" : "<?php echo $this->default_loader_graphic; ?>"});
</script>