<div class="participant-video-controls">
    <div class="control-slider-area-cntnr">
        <div class="control-slider-cntnr">
            <a class="taggable-area" data-toggle="popover" data-placement="top">
                <span><?php _e('(Click to tag or comment)', 'glp'); ?></span>
            </a>
            <div class="popover-data hide">
                <div class="title"><div class="inner">Comments/Tags (<span class="time"></span>)<a class="icon-remove-circle icon-white close"></a></div></div>
                <div class="content">
                    <form method="post">
                        <div class="inner">
                            <div class="comment-box">
                                <input type="text" name="comment" placeholder="Comment" />
                            </div>
                        </div>
                        <div class="tags-box">
                            <div class="tags">Tags: <span>open field</span> <span>night</span></div>
                            <div class="add-tag">&#43; Tag</div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="clip-markers">
                <?php
                $markers = array();
                foreach ( get_comments( array( 'post_id' => get_the_ID()) ) as $comment) {
                    $time_meta = get_comment_meta($comment->comment_ID, 'clip_time', true);
                    $time = (int) ( $time_meta['m'] * 60 ) + (int) $time_meta['s'];
                    $markers[$time_meta['p']]['comments'][] = $comment;
                }
                ?>
                <?php foreach ($markers as $time => $items): ?>
                    <a id="marker-<?php echo $time; ?>" class="marker" style="left: <?php printf('%spx', $time); ?>">
                        <div class="arrow"></div>
                        <div class="hide content">
                            <?php foreach ($items['comments'] as $comment): ?>
                                <?php include(locate_template('templates/marker-comment.php')); //Keeps variable scope where get_template_part does not ?>
                            <?php endforeach; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
            
            <div class="control-slider"></div>
        </div>
        <div class="control-time">
            <span class="control-time-current"><span class="time-m"></span>:<span class="time-s"></span></span>
            <span class="control-time-sep">&#47;</span>
            <span class="control-time-total"><span class="time-m"></span>:<span class="time-s"></span></span>
        </div>
    </div>
    <div class="control-buttons-cntnr">
        <a data-control="play" class="controls-play controls"><span class="icon-play icon-white"></span></a>
        <a data-control="pause" class="controls-pause controls"><span class="icon-pause icon-white"></span></a>
    </div>
</div>