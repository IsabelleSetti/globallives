<?php global $participants; ?>

<div id="gridview" class="view">
	<div class="container">
<?php
	foreach ($participants as $participant) {
		$participant_id = $participant->ID;
?>
		<article id="participant-<?php echo $participant_id; ?>" class="participant-grid<?php the_participant_field('proposed', $participant_id) ? ' proposed hide' : ''; ?>">
			<a href="<?php echo get_permalink($participant_id); ?>">
				<div class="participant-meta">
					<h3><?php echo $participant->post_title; ?></h3>
					<p>
						<?php the_participant_field('occupation', $participant_id); ?>
						<?php _e('in','glp'); ?>
						<?php the_participant_field('location', $participant_id); ?>
					</p>
<?php
		if ($themes = get_participant_themes($participant_id)) {
?>
					<p>
						<b><?php _e('Themes: ','glp'); ?></b>
						<?php the_participant_themes($participant_id); ?>
					</p>
<?php
		}
?>
				</div>
				<img src="<?php the_participant_thumbnail_url($participant_id, 'small'); ?>">

			</a>
		</article>
<?php
	}
?>
	</div>
</div>