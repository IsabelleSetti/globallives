<?php

/*	==========================================================================
	User
	========================================================================== */

	add_action('init', 'set_profile_base');
	function set_profile_base() {
		global $wp_rewrite;
		$wp_rewrite->author_base = 'profile';
		$wp_rewrite->author_structure = '/' . $wp_rewrite->author_base . '/%author%';
		$wp_rewrite->flush_rules();
	}

// Advanced Custom Fields helpers

	function get_profile_field($field, $user_id) {
		global $field_keys;
		return get_field($field_keys['user_' . $field], 'user_' . $user_id);
	}
	function the_profile_field($field, $user_id) {
		echo get_profile_field($field, $user_id);
	}
	function update_profile_field($field, $value, $user_id) {
		global $field_keys;
		update_field($field_keys['user_' . $field], $value, 'user_'.$user_id);
	}

// Useful functions

	function is_profile_created($user_id) {
		$user = get_userdata($user_id);
		return ( // Check all required fields
			$user->user_firstname &&
			$user->user_lastname &&
			get_profile_field('occupation', $user_id) &&
			get_profile_field('location', $user_id)
		);
	}

	function get_fullname($user_id) {
		$user = get_userdata($user_id);
		return $user->user_firstname . ' ' . $user->user_lastname;
	}
	function the_fullname($user_id) {
		echo get_fullname($user_id);
	}

	function get_profile_thumbnail_url( $user_id, $size = 'thumbnail' ) {
		// First try the Profile page
		if ($user_avatar = get_profile_field('avatar', $user_id)) {
			if ($user_avatar['sizes'][$size] != '') { return $user_avatar['sizes'][$size]; }
		}
		// Then try Social Login
		if ($social_login = get_usermeta($user_id, 'oa_social_login_user_picture')) {
			return $social_login;
		}
		// Then try Gravatar
		if ($get_avatar = get_avatar($user_id, $size)) {
			preg_match("/src='(.*?)'/i", $get_avatar, $matches);
			return $matches[1];
		}
		// Finally, give the default
		return get_bloginfo('template_directory') . '/img/logo-coda.png';
	}
	function the_profile_thumbnail_url( $user_id, $size = 'thumbnail' ) {
		echo get_profile_thumbnail_url( $user_id, $size );
	}

// My Library

	function get_library_participants($user_id) {
		$library = get_profile_field('library', $user_id);
		$participants = array();
		foreach ($library as $clip) {
			$participant = get_posts(array(
				'post_type' => 'participant',
				'posts_per_page' => 1,
				'meta_query' => array(array(
					'key' => 'clips',
					'value' => '"' . $clip->ID . '"',
					'compare' => 'LIKE'
				))
			));
			if ($participant && !in_array($participant[0], $participants)) { $participants[] = $participant[0]; }
		}
		return $participants;
	}

	function get_library_participant_clips($user_id, $participant_id) {
		$library = get_profile_field('library', $user_id);
		$participant_clips = get_participant_field('clips', $participant_id);
		$clips = array();
		foreach ($library as $clip) {
			if (in_array($clip, $participant_clips)) { $clips[] = $clip; }
		}
		return $clips;
	}

	function get_library_clip_tags($user_id) {
		$clip_tags = array();
		if ($clips = get_profile_field('library', $user_id)) {
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

	function clip_toggle_queue_response($response, $in_library) {
		if ($in_library) { $response = __('&#45; Library', 'glp'); }
		else { $response = __('&#43; Library', 'glp'); }
		return $response;
	}
	add_filter('clip_toggle_response', 'clip_toggle_queue_response', 1, 3);

	function clip_toggle_list_response($response, $all_in_library) {
		if (true !== $all_in_library) { $response = __('&#45; All from Library', 'glp'); }
		else { $response = __('&#43; All to Library', 'glp'); }
		return $response;
	}
	add_filter('clip_toggle_list_response', 'clip_toggle_list_response', 1, 3);

	function clip_toggle_library_status($response, $clip_id, $user_id) {
		$in_library = is_clip_in_library($clip_id, $user_id);
		$response = apply_filters('clip_toggle_response', $response, $in_library);
		return $response;
	}
	add_filter('clip_toggle_library_status', 'clip_toggle_library_status', 1, 3);

	function clip_toggle_library_list_status($response, $user_id) {
		$clips = get_participant_field('clips', $user_id);
		$response = apply_filters('clip_toggle_list_response', $response, is_list_in_library($clips, $user_id));
		return $response;
	}
	add_filter('clip_toggle_library_list_status', 'clip_toggle_library_list_status', 1, 3);

	function is_clip_in_library($clip_id, $user_id) {
		if ($library = get_profile_field('library', $user_id)) {
			foreach ($library as $clip_index => $clip) {
				if ($clip_id === $clip->ID) { return $clip_index; }
			}
		}
		return false;
	}

	function is_list_in_library($clip_list, $user_id) {
		if ($queue = get_profile_field('library', $user_id)) {
			foreach ($queue as $clip) {
				$queued = array_search($clip, $clip_list);
				if (is_int($queued)) {
					unset($clip_list[$queued]);
				}
			}
		}
		if (empty($clip_list)) { return true; }
		else { return $clip_list; }
	}

	function clean_relationship_type_queue($queue) {
		foreach ($queue as $k => $v) {
			if (is_object($v) && ('WP_Post' == get_class($v))) { $queue[$k] = $v->ID; }
		}
		return $queue;
	}
	add_filter('clean_queue', 'clean_relationship_type_queue');

// OneAll Social Login

	function update_profile_from_social ($profile, $identity) {
		$provider = $identity->provider;
		$id = end(explode('/', $identity->id));
		$connections = array(array($provider => $id));
		update_profile_field('connections', $connections, $profile->ID);
	}
	add_action ('oa_social_login_action_after_user_insert', 'update_profile_from_social', 10, 2);