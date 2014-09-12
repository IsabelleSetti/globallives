<?php while(have_posts()) : the_post(); $participant_id = get_the_ID(); ?>
<article id="post-<?php echo $participant_id; ?>" class="participant-summary"<?php if (has_post_thumbnail()) : ?> data-bg="<?php the_participant_thumbnail_url($participant_id, 'large'); ?>"<?php endif; ?>>
	<h2 class="participant-title"><?php the_title(); ?><span class="participant-location"> &mdash; <?php the_participant_field('location', $participant_id); ?></span></h2>
	<div class="row">
		<div class="col-sm-4">
			<a class="btn btn-default map-explore" href="<?php the_permalink(); echo "#mapview"; ?>"><i class="icon icon-globe"></i> <?php _e('Explore in map','glp'); ?></a>
			<?php $lat = get_participant_field('latitude', $participant_id); $long = get_participant_field('longitude', $participant_id); ?>
			<a href="https://maps.google.com/maps?q=loc:<?php echo $lat; ?>,<?php echo $long; ?>&hl=en&ll=<?php echo $lat; ?>,<?php echo $long; ?>&z=6" target="new"><img class="participant-map" src="http://maps.googleapis.com/maps/api/staticmap?center=<?php echo $lat; ?>,<?php echo $long; ?>&zoom=6&size=570x250&markers=color:red|<?php echo $lat; ?>,<?php echo $long; ?>&maptype=roadmap&sensor=false&style=feature:all%7Celement:geometry%7Csaturation:-100"></a>
			<div class="participant-meta row">
	    		<div class="col-sm-6">
	    			<b><?php _e('Occupation','glp'); ?>:</b> <?php the_participant_field('occupation', $participant_id); ?><br>
	    			<?php if ($dob = get_participant_field('dob', $participant_id)) : ?><b><?php _e('Date of Birth','glp'); ?>:</b> <?php echo $dob; ?><?php endif; ?>
	    		</div>
	    		<div class="col-sm-6">
	    			<b><?php _e('Religion','glp'); ?>:</b> <?php the_participant_field('religion', $participant_id); ?><br>
	    			<b><?php _e('Income','glp'); ?>:</b> <?php $incomes = get_participant_object('income'); $income = get_participant_field('income', $participant_id); echo $incomes['choices'][$income]; ?>
	    		</div>
	    	</div>
	    	<div class="participant-content">
			    <?php the_content(); ?>
			</div>
			<a class="btn btn-default" href="<?php the_permalink(); ?>">&#9658;&nbsp;<?php _e('Learn More','glp'); ?></a>
		</div>
		<div class="col-sm-8">
<?php
	if ( $summary_video = get_participant_field('summary_video', $participant_id) ) {
		query_posts(array( 'post_type' => 'clip', 'p' => $summary_video[0]->ID ));
		get_template_part('templates/clip','summary');
		wp_reset_query();
	}
?>
	</div>
</article>
<?php endwhile; ?>