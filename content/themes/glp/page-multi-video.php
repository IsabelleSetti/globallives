<?php $participants = get_posts(array( 'post_type' => 'participant', 'posts_per_page' => 6, 'orderby' => 'rand' )); ?>

<div class="multi-video-container">
<!--Basic example video loop, feed it any number of clips. Clips need to be aware to their participant owner -->
<?php foreach ($participants as $participant): ?>
    <?php if ( $clips = get_field('clips', $participant->ID)) : $clip = $clips[ array_rand($clips) ]; ?>
        <div class="multi-video-item" id="multi-video-<?php echo $clip->ID; ?>" data-clip_id="<?php echo $clip->ID; ?>">
            <?php include(locate_template('templates/clip-embed.php')); ?>
            <div class="participant-meta">
                    <h3><?php echo $participant->post_title; ?></h3>
                    <p><?php the_field('location',$participant->ID); ?></p>
                    <p><?php the_field('occupation',$participant->ID); ?></p>
            </div>
        </div>
    <?php endif; ?>
<?php endforeach; ?>
    
    <div class="multi-video-controls">
        <a data-control="play" data-multi="true" class="controls btn btn-inverse play-pause"><span class="icon-play icon-white"></span></a>
    </div>
</div>
