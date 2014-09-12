<?php
	$serieses = get_terms('series');
	$genders = get_participant_object('gender');
	$incomes = get_participant_object('income');
	$ages = get_participant_object('age');
	$proposed = get_proposed_participants();
?>

<nav id="nav-explore" class="navbar">
	<div class="nav-explore-inner container">
		<ul class="nav">

			<li class="btn-group">
				<a class="btn btn-default btn-gridview active"><i class="fa fa-th-large fa-white"></i> <?php _e('Grid View','glp'); ?></a>
				<a class="btn btn-default btn-mapview"><i class="fa fa-globe fa-white"></i> <?php _e('Map View','glp'); ?></a>
			</li>

<?php
	if (count($serieses) > 1) {
?>
			<li>Series
				<select class="btn btn-default" name="series">
					<option>All</option>
<?php
		foreach ($serieses as $series) {
?>
					<option value="<?php echo $series->slug; ?>"><?php echo $series->name; ?></option>
<?php
		}
?>
				</select>
			</li>
<?php
	}
?>

			<li>Gender
				<select class="btn btn-default" name="gender">
					<option>All</option>
<?php
	foreach ($genders['choices'] as $k => $v) {
?>
					<option value="<?php echo $k; ?>"><?php echo $v; ?></option>
<?php
	}
?>
				</select>
			</li>

			<li>Income
				<select class="btn btn-default" name="income">
					<option>All</option>
<?php
	foreach ($incomes['choices'] as $k => $v) {
?>
					<option value="<?php echo $k; ?>"><?php echo $v; ?></option>
<?php
	}
?>
				</select>
			</li>

			<li>Age
				<select class="btn btn-default" name="age">
					<option>All</option>
<?php
	foreach ($ages['choices'] as $k => $v) {
?>
					<option value="<?php echo $k; ?>"><?php echo $v; ?></option>
<?php
	}
?>
				</select>
			</li>

<?php
	if (count($proposed)) {
?>
			<li><input name="proposed" type="checkbox" /> <?php _e('Show Proposed','glp'); ?></li>
<?php
	}
?>

		</ul>
	</div>
</nav>