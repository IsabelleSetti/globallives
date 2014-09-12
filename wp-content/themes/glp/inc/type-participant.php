<?php
	global $field_keys;

/*	==========================================================================
	Participant
	========================================================================== */

	function create_participant_post_type() {

   		register_taxonomy('series', 'participant', array(
			'label' => __('Series'),
			'rewrite' => array(
				'slug' => 'series',
				'with_front' => false
			)
		));
   		register_taxonomy('themes', 'participant', array(
			'label' => __('Themes'),
			'rewrite' => array(
				'slug' => 'themes',
				'with_front' => false
			)
		));

		register_post_type('participant', array(
			'labels' => array(
			    'name'			=> __('Participants'),
			    'singular_name'	=> __('Participant')
			),
			'public' => true,
			'supports' => array('title', 'editor', 'thumbnail', 'revisions', 'page-attributes'),
			'menu_position' => 5,
			'has_archive' => true,
			'rewrite' => array(
			    'slug'			=> 'participants',
			    'with_front'	=> false
			)
		));
	}
	add_action('init', 'create_participant_post_type');

	function get_participants ($max = 12, $orderby = 'rand') {
		return get_posts(array(
			'post_type' => 'participant',
			'posts_per_page' => $max,
			'orderby' => $orderby
		));
	}
	function get_proposed_participants() {
		return get_posts(array(
			'post_type' => 'participant',
			'posts_per_page' => -1,
			'meta_key' => 'proposed',
			'meta_value' => 1
		));
	}

	add_action('acf/save_post','update_participant_fields', 20);
	function update_participant_fields($participant_id) {
		global $field_keys;
		// Auto update fields based on other fields
	}
	function get_participant_field ($field, $participant_id) {
		global $field_keys;
		return get_field($field_keys['participant_' . $field], $participant_id);
	}
	function the_participant_field($field, $participant_id) {
		echo get_participant_field($field, $participant_id);
	}
	function update_participant_field($file, $value, $participant_id) {
		global $field_keys;
		update_field($field_keys['participant_' . $field], $value, $participant_id);
	}
	function get_participant_object($field) {
		global $field_keys;
		return get_field_object($field_keys['participant_' . $field]);
	}

	function get_participant_thumbnail_url($participant_id, $thumbnail_size = 'thumbnail') {
		$participant = get_post($participant_id);
		$thumbnail = wp_get_attachment_image_src(get_post_thumbnail_id($participant->ID), $thumbnail_size);
		return $thumbnail[0];
	}
	function the_participant_thumbnail_url($participant_id, $thumbnail_size = 'thumbnail') {
		echo get_participant_thumbnail_url($participant_id, $thumbnail_size);
	}

	function get_participant_taxonomy_slugs($participant_id, $taxonomy) {
		$term_slugs = array();
		if ($participant_terms = get_the_terms($participant_id, $taxonomy)) {
			foreach ($participant_terms as $term) {
				$term_slugs[] = $term->slug;
			}
		}
		return $term_slugs;
	}

	function get_participant_clip_tags($participant_id) {
		$clip_tags = array();
		if ($clips = get_field($field_keys['participant_clips'],$participant_id)) {
			foreach($clips as $clip) {
				if ($this_clip_tags = get_the_terms($clip->ID, 'clip_tags')) {
					$clip_tags += $this_clip_tags;
				}
			}
			return $clip_tags;
		} else {
			return false;
		}
	}
	function get_participant_themes($participant_id, $limit = 5) {
		$themes = array();
		if ($clips = get_participant_field('clips', $participant_id)) {
			foreach ($clips as $clip) {
				foreach(get_clip_tags($clip->ID) as $clip_tag) {
					if (array_key_exists($clip_tag, $themes)) {
						$themes[$clip_tag] += 1;
					} else {
						$themes[$clip_tag] = 1;
					}
				}
			}
			arsort($themes);
			return array_keys(array_slice($themes, 0, $limit));
		} else {
			return false;
		}
	}
	function the_participant_themes($participant_id, $separator = ", ") {
		$themes = get_participant_themes($participant_id);
		echo implode($separator, $themes);
	}

	function get_participants_themes($participants, $limit = 5) {
		$themes = array();
		foreach ($participants as $participant) {
			$participant_themes = get_participant_themes($participant->ID, $limit);
			foreach ($participant_themes as $theme) {
				if (array_key_exists($theme, $themes)) {
					$themes[$theme] += 1;
				} else {
					$themes[$theme] = 1;
				}
			}
		}
		arsort($themes);
		return array_keys(array_slice($themes, 0, $limit));
	}

	function get_participants_by_theme($theme, $participants = null) {
		if (!isset($participants)) { $participants = get_participants(-1); }

		$theme_participants = array();
		foreach ($participants as $participant) {
			$participant_themes = get_participant_themes($participant->ID);
			if (in_array($theme, $participant_themes)) { $theme_participants[] = $participant; }
		}
		return $theme_participants;
	}

	function get_participant_crew_members($participant_id) {
		global $field_keys;
		$crew_members = get_users(array(
			'meta_query' => array(
				array(
					'key' => 'shoots',
					'compare' => 'LIKE',
					'value' => '"' . $participant_id . '"'
				)
			)
		));
		return $crew_members;
	}

	function get_related_participants($participant_id, $taxonomy = 'themes') {

		$tax_ids = wp_get_post_terms($participant_id, $taxonomy, array('fields' => 'ids'));

		if ($tax_ids) { // Get participants that share taxonomy
			$related_participants = get_posts(array(
				'post_type'			=> 'participant',
				'posts_per_page'	=> -1,
				'tax_query'			=> array(array(
					'taxonomy' 	=> $taxonomy,
					'field'		=> 'id',
					'terms'		=> $tax_ids,
					'operator' 	=> 'IN'
				))
			));
			return $related_participants;
		} else {
			return false;
		}
	}

	function participantsToJSON($participants) {
		$JSON = array();
		foreach ($participants as $participant) {
			$JSON[] = participantToJSON($participant->ID);
		}
		return '[' . implode(',', $JSON) . ']';
	}

	function participantToJSON($participant_id) {
		return json_encode(array(

			'id' => $participant_id,
			'name' => get_the_title($participant_id),
			'occupation' => get_participant_field('occupation', $participant_id),
			'location' => get_participant_field('location', $participant_id),
			'dob' => get_participant_field('dob', $participant_id),

			'latitude' => get_participant_field('latitude', $participant_id),
			'longitude' => get_participant_field('longitude', $participant_id),
			'continent' => get_participant_field('continent', $participant_id),

			'themes' => get_participant_themes($participant_id),
			'theme_labels' => '',

			'series' => get_participant_taxonomy_slugs($participant_id, 'series'),
			'series_labels' => get_the_term_list($participant_id, 'series', '', ', '),

			'gender' => get_participant_field('gender', $participant_id),
			'income' => get_participant_field('income', $participant_id),
			'age' => get_participant_field('age', $participant_id),
			'proposed' => get_participant_field('proposed', $participant_id),
			'filtered' => false,

			'thumbnail' => get_participant_thumbnail_url($participant_id),
			'permalink' => get_permalink($participant_id)

		));
	}

/*	{
		name: '<?php echo $participant->post_title; ?>',
		occupation: '<?php the_field($field_keys['participant_occupation'], $participant->ID); ?>',
		location: '<?php the_field($field_keys['participant_location'], $participant->ID); ?>',
		dob: '<?php the_field($field_keys['participant_dob'], $participant->ID); ?>',

		latitude: <?php the_field($field_keys['participant_latitude'], $participant->ID); ?>,
		longitude: <?php the_field($field_keys['participant_longitude'], $participant->ID); ?>,
		continent: '<?php the_field($field_keys['participant_continent'], $participant->ID); ?>',

		series: ['<?php echo implode("','",get_participant_taxonomy_slugs($participant->ID,'series')); ?>'],
		series_labels: '<?php echo get_the_term_list($participant->ID,'series','',', '); ?>',
		themes: ['<?php echo implode("','",get_participant_taxonomy_slugs($participant->ID,'themes')); ?>'],
		theme_labels: '<?php echo get_the_term_list($participant->ID,'themes','',', '); ?>',
		gender: '<?php the_field($field_keys['participant_gender'], $participant->ID); ?>',
		income: '<?php the_field($field_keys['participant_income'], $participant->ID); ?>',
		age: '<?php the_field($field_keys['participant_age'], $participant->ID); ?>',
		proposed: '<?php the_field($field_keys['participant_proposed'], $participant->ID); ?>',
		filtered: false,

		id: <?php echo $participant->ID; ?>,
		thumbnail: '<?php the_participant_thumbnail_url( $participant->ID ); ?>',
		permalink: '<?php echo get_permalink($participant->ID); ?>'
	}
		return '{' . json_encode($JSON) . '}';
	}
*/