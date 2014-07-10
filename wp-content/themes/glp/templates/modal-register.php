<div id="modal-register" class="modal hide">
	<div class="modal-header">
		<div class="row-fluid">
			<h3 class="span6"><?php _e('Join Global Lives','glp'); ?></h3>
			<span class="span6 text-right">Already a member? <a class="login-toggle">Log in</a></span>
		</div>
	</div>
	<div class="modal-body row-fluid">
		<div class="span6">
			<p><?php _e('Create your account','glp'); ?></p>
			<form name="registerform" id="registerform" action="<?php echo site_url('wp-login.php?wpe-login=globallives&action=register'); ?>" method="post">
				<div id="registerform-alert" class="alert hide"></div>
				<p><input type="text" name="user_email" id="user_email" class="input" value="" placeholder="<?php _e('Email Address','glp');?>"></p>
				<p><input type="text" name="user_login" id="user_login" class="input" value="" placeholder="<?php _e('Username','glp'); ?>"></p>
				<p><input type="password" name="user_pass" id="user_pass" class="input" value="" placeholder="<?php _e('Password','glp'); ?>"></p>
				<p><input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large" value="<?php _e('Create my account','glp'); ?>"></p>
				<input type="hidden" name="redirect_to" id="redirect_to" value="<?php echo site_url('/profile'); ?>">
				<?php wp_nonce_field('glp_user_registration','user_nonce', true, true ); ?>
			</form>
		</div>
		<div class="span6">
			<p><?php _e('Or sign up with:','glp'); ?></p>
			<?php do_action('oa_social_login'); ?>
		</div>
	</div>
</div>