<?php $post_id = $blog_post->ID; ?>
<article id="post-<?php echo $post_id; ?>" class="result result-post row">
<?php if( has_post_thumbnail($post_id) ) : ?>
	<div class="post-thumbnail col-md-3"><?php echo get_the_post_thumbnail($post_id, 'medium'); ?></div>
	<div class="col-md-9">
<?php else : ?>
	<div class="col-md-12">
<?php endif; ?>
		<h4><a href="<?php the_permalink(); ?>"><?php echo $blog_post->post_title; ?></a></h4>
		<p><?php echo wp_trim_words($blog_post->post_content, 40); ?>
	</div>
</article>