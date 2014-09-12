<div id="modal-login" class="modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<div class="row">
					<h3 class="col-md-6"><?php _e('Log In','glp'); ?></h3>
					<a class="col-md-6 text-right register-toggle">Join Global Lives</a>
				</div>
			</div>
			<div class="modal-body row">
				<div class="col-md-6">
					<p><?php _e('Your Global Lives account:', 'glp'); ?></p>
					<form name="loginform" id="loginform" action="<?php echo site_url('wp-login.php?wpe-login=globallives', 'login_post'); ?>" method="post">
						<p><input type="text" name="log" id="user_login" class="form-control" value="" size="20" placeholder="Username"></p>
						<p><input type="password" name="pwd" id="user_pass" class="form-control" value="" size="20" placeholder="Password"></p>
						<p><label for="rememberme"><input type="checkbox" name="rememberme" value="forever" checked> Remember me - <a href="<?php echo site_url('wp-login.php?action=lostpassword'); ?>"> Can't log in?</a></label></p>
						<p><input type="submit" name="wp-submit" id="wp-submit" class="btn btn-primary btn-large" value="<?php _e('Log In','glp'); ?>" /></p>
						<input type="hidden" name="redirect_to" value="<?php the_permalink(); ?>" />
					</form>
				</div>
				<div class="col-md-6">
					<p><?php _e('Or log in with:','glp'); ?></p>
					<?php do_action('oa_social_login'); ?>
				</div>
			</div>
		</div>
	</div>
</div>