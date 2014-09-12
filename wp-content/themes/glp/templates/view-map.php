<?php global $participants; ?>
<script>var participants = <?php echo participantsToJSON($participants); ?>;</script>

<div id="mapview" class="view"></div>
<div class="overlay"></div>

<div id="popover" class="col-md-4">
	<h2><span class="popover-name"></span> &mdash; <span class="popover-location"></h2>
	<div class="row">
		<div class="col-sm-7">
			<div class="row"><b class="col-sm-6"><?php _e('Occupation','glp'); ?>:</b> <span class="col-sm-6 popover-occupation"></span></div>
			<div class="row"><b class="col-sm-6"><?php _e('Date of Birth','glp'); ?>:</b> <span class="col-sm-6 popover-dob"></span></div>
		</div>
		<div class="col-sm-5">
			<a class="btn btn-default popover-permalink" href="">&#9658;&nbsp;<?php _e('Watch Video','glp'); ?></a>
		</div>
	</div><br>

	<p><b><?php _e('Themes','glp'); ?>:</b><br><span class="popover-themes"><?php _e('none','glp'); ?></span></p>
	<p><b><?php _e('Series','glp'); ?>:</b><br><span class="popover-series"><?php _e('none','glp'); ?></span></p>

	<button type="button" class="close">&times;</button>
</div>