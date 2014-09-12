<?php
	global $participants;
	$themes = get_participants_themes($participants, 10);
?>

<nav id="nav-themes">
	<div class="nav-themes-inner container">
		<div id="theme-carousel" class="carousel slide">
			<ul class="carousel-inner">
				<li class="theme-navitem col-md-2 active"><?php echo _e('All Themes','glp'); ?></li>

<?php
	$total_themes = count($themes);
	$themes_per_row = 4;
	foreach($themes as $i => $theme) {
?>
				<?php if ($i % $themes_per_row == 0) : ?><div class="item row<?php if ($i == 0) :?> active<?php endif; ?>"><?php endif; ?>

				<li class="theme-navitem col-md-2" data-term="<?php echo $theme; ?>">
					<a class="theme-link hide" href="/themes/<?php echo $theme; ?>">
						<div class="theme-watch"><?php _e('Watch this theme','glp'); ?></div>
						<div class="thumbnails">

<?php
		if ($theme_participants = get_participants_by_theme($theme, $participants)) {
			foreach ($theme_participants as $theme_participant) {
?>
							<img src="<?php the_participant_thumbnail_url( $theme_participant->ID, 'small' ); ?>">
<?php
		}
	}
?>
						</div>
					</a>
					<a class="theme-filter"><?php echo $theme; ?></a>
				</li>

				<?php if ($i == $total_themes - 1 || $i % $themes_per_row == ($themes_per_row - 1)) : ?></div><?php endif; ?>

<?php
	}
?>

			</ul>
			<?php if ($total_themes > $themes_per_row) : ?><a class="carousel-control right" href="#theme-carousel" data-slide="next">&#9654;</a><?php endif; ?>
		</div>
	</div>
</nav>