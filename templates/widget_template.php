<?php
if(!empty($description) && trim($description) != '') {
	echo "<p>".$description."</p>";
}
?>
<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" id="<?php echo $form_id; ?>" method="post">
	<input type="hidden" name="ns_mc_number" value="<?php echo $this->number; ?>" />

	<?php echo $this->subscribe_errors; ?>

	<?php	if($instance['collect_first']): ?>
	<div class="form-group">
		<input class='form-control' placeholder="First Name" type="text" name="<?php echo $this->id_base . '_first_name'; ?>" />
	</div>
	<?php endif; ?>

	<?php if($instance['collect_last']): ?>
	<div class="form-group">
		<input class='form-control' placeholder="Last Name" type="text" name="<?php echo $this->id_base . '_last_name'; ?>" />
	</div>
	<?php	endif; ?>

	<div class="form-group">
		<label>Email Address</label>
		<input class='form-control' placeholder="Enter Email Address" id="<?php echo $this->id_base; ?>-email-<?php echo $this->number; ?>" type="text" name="<?php echo $this->id_base; ?>_email" />
	</div>

	<input class="btn btn-success" type="submit" name="<?php echo __($instance['signup_text'], 'mailchimp-widget'); ?>" value="<?php echo __($instance['signup_text'], 'mailchimp-widget'); ?>" />
	<?php if(!empty($learn_more)): ?><a href="<?php echo $learn_more; ?>" class='btn btn-primary'>Learn More</a><?php endif; ?>
</form>
<script>
jQuery('#<?php echo $form_id; ?>').mailchimp_widget({
	"url":"<?php echo $_SERVER['PHP_SELF']; ?>",
	"cookie_id" : "<?php echo $this->id_base; ?>-<?php echo $this->number; ?>",
	"cookie_value" : "<?php echo $this->hash_mailing_list_id(); ?>"
});
</script>