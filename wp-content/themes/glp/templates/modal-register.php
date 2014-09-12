<div id="modal-register" class="modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<div class="row">
					<h3 class="col-md-6"><?php _e('Join Global Lives','glp'); ?></h3>
					<span class="col-md-6 text-right">Already a member? <a class="login-toggle">Log in</a></span>
				</div>
			</div>
			<div class="modal-body row">
				<div class="col-md-6">
					<p><?php _e('Create your account','glp'); ?></p>
					<form name="registerform" id="registerform" action="<?php echo site_url('wp-login.php?wpe-login=globallives&action=register'); ?>" method="post">
						<div id="registerform-alert" class="alert hide"></div>
						<p><input type="text" name="user_email" id="user_email" class="form-control" value="" placeholder="<?php _e('Email Address','glp');?>"></p>
						<p><input type="text" name="user_login" id="user_login" class="form-control" value="" placeholder="<?php _e('Username','glp'); ?>"></p>
						<p><input type="password" name="user_pass" id="user_pass" class="form-control" value="" placeholder="<?php _e('Password','glp'); ?>"></p>
						<p><input type="submit" name="wp-submit" id="wp-submit" class="btn btn-primary btn-large" value="<?php _e('Create my account','glp'); ?>"></p>
						<input type="hidden" name="redirect_to" id="redirect_to" value="<?php echo site_url('/profile'); ?>">
						<?php wp_nonce_field('glp_user_registration','user_nonce', true, true ); ?>
					</form>
				</div>
				<div class="col-md-6">
					<p><?php _e('Or sign up with:','glp'); ?></p>
					<?php do_action('oa_social_login'); ?>
				</div>
			</div>
		</div>
	</div>
</div>