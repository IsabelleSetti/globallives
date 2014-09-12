$(function() {
	'use strict';

	// Functions

	function showTerm (ev) {
		ev.preventDefault();
		var tax = ev.data.taxonomy,
			hub = $.grep(participants, function (p) { return p.id === ev.data.participant; })[0],
			term = $(this).text();

		clearTerms();
		$(participants).each(function () {
			if (this.id !== hub.id) {
				var shared = sharedTerms(hub[tax], this[tax]);
				if (shared.length && $.inArray(term, shared) > -1) {
					drawTerm(hub, this);
					console.log('shared',shared,'term',term);
				}
			}
		});
	}

	function sharedTerms (a, b) {
		if (a.length && b.length) {
			return $.grep(a, function (i) { return a !== '' ? $.inArray(i, b) > -1 : false; });
		} else {
			return false;
		}
	}

	function drawTerm (hub, spoke) {
		var hub_marker = map.select('#marker-' + hub.id),
			spoke_marker = map.select('#marker-' + spoke.id),
			hub_xy = [+hub_marker.attr('data-x'), +hub_marker.attr('data-y')],
			spoke_xy = [+spoke_marker.attr('data-x'), +spoke_marker.attr('data-y')],

			edge = underlay.append('path')
				.attr('id', function () { return 'edge-' + spoke.id; })
				.attr('class', function () { return spoke.continent ? 'edge ' + spoke.continent : 'edge'; })
				.attr('d', function () { return 'M' + hub_xy.join() + 'L' + spoke_xy.join() + 'Z'; });

			var edgeLength = $('#edge-' + spoke.id).get(0).getTotalLength();
			edge.style('stroke-dasharray', edgeLength)
				.style('stroke-dashoffset', edgeLength);
	}

	function clearTerms () {
		map.selectAll('.edge').remove();
	}

	function updatePopover (d, el) {
		var width = $('#mapview').width(),
			dy = $(el).position().top,
			dx = $(el).position().left,
			dx_offset;
		if ( dx < width / 2 ) {
			dx_offset = 60;
		} else {
			dx_offset = -390;
		}

		$('#popover').attr('data-participant_id',d.id);
		$('#popover').css('top', dy).css('left', dx + dx_offset);

		$('#popover .popover-name').text(d.name);
		$('#popover .popover-location').text(d.location);
		$('#popover .popover-permalink').attr('href', d.permalink);
		$('#popover .popover-occupation').text(d.occupation);
		$('#popover .popover-dob').text(d.dob);

		$('#popover .popover-series').html(d.series_labels || '');
		$('#popover .popover-themes').html(d.themes.map(function (theme) {
			return '<a>' + theme + '</a>';
		}) || '');

		$('#popover').show();
		$('.mapthumb').attr('class', 'mapthumb');
		$(el).find('.mapthumb').attr('class', 'mapthumb popped');

		$('.popover-series a').on('click', {taxonomy: 'series', participant: d.id }, showTerm);
		$('.popover-themes a').on('click', {taxonomy: 'themes', participant: d.id }, showTerm);

		clearTerms();
	}

	// Main

	var single_participant_id = $('article.participant').attr('data-participant_id');

	if (!window.location.hash || window.location.hash !== 'mapview') { $('#mapview').hide(); }

	$('#mapview').css('max-height', function() {
		return $(window).height() - $('#content').offset().top - $('#nav-explore').height() - $('#nav-themes').height() - $('.handle').height();
		});

	var height = $('#mapview').height(),
		width = height * 2;

	// D3 Functions
	var	projection = d3.geo.mercator()
		.scale( width * 0.16 )
		.translate([width / 2, height / 1.75]);
	var path = d3.geo.path().projection(projection);

	// SVG groups
	var map = d3.select('#mapview').append('svg')
		.attr('height', height).attr('width', width);
	var countries = map.append('g').attr('id', 'countries');

	countries.append('rect').attr('class', 'background')
		.attr('height', height).attr('width', width);
	var underlay = map.append('g').attr('id','underlay');

	// Set up Participant thumbnails as SVG patterns

	var thumbnails = map.append('defs').selectAll('thumbnails')
		.data(participants)
		.enter().append('pattern')
			.attr('id', function (d, i) { return 'image-' + i; })
			.attr('patternUnits', 'objectBoundingBox')
			.attr('width', 50).attr('height', 50)
		.append('image')
			.attr('xlink:href', function (d) { return d.thumbnail; })
			.attr('x', 0).attr('y', 0)
			.attr('width', function (d){ return (single_participant_id == d.id) ? 64 : 48; })
			.attr('height', function (d){ return (single_participant_id == d.id) ? 64 : 48; });

	// Add markers for each Participant

	var marker = map.selectAll('.marker')
		.data(participants)
		.enter().append('g')
			.attr('id', function (d) { return 'marker-' + d.id; })
			.attr('class', function (d) { return 'marker ' + d.continent + (d.proposed ? ' proposed' : ''); })
			.attr('transform', function (d) { return 'translate(' + projection([+d.longitude, +d.latitude]) + ')'; })
			.attr('data-x', function (d) { var coords = projection([+d.longitude, +d.latitude]); return Math.round(coords[0]); })
			.attr('data-y', function (d) { var coords = projection([+d.longitude, +d.latitude]); return Math.round(coords[1]); })
				.on('click', function (d) { updatePopover(d, this); });

		marker.append('circle').attr('class', 'pin') // Add the pins
			.attr('r', 5);

		marker.append('circle').attr('class', function(d){ return (single_participant_id == d.id) ? 'mapthumb single' : 'mapthumb'; }) // Add the map thumbs
			.attr('id', function (d, i) { return 'mapthumb-'+i; })
			.attr('r', function (d){ return (single_participant_id == d.id) ? 32 : 24; })
			.attr('fill',function (d, i) { return 'url(#image-'+i+')';});

	// Load the low-res country outlines, followed by hi-res to replace it when its ready
	d3.json('/wp-content/themes/glp/dist/countries.json', function( json ) {
		countries.selectAll('path').data(json.features).enter().append('svg:path').attr('d', path);
	});
	d3.json('/wp-content/themes/glp/dist/countries-hires.json', function( json ) {
		countries.selectAll('path').remove();
		countries.selectAll('path').data(json.features).enter().append('svg:path').attr('d', path);
	});


	// Show/Hide Interactions

	$('.overlay, .mapthumb:not(.single), #popover').hide();

	$('.marker').hover(function() {
		$('.mapthumb:not(.single, .popped)').hide();
		$(this).find('.mapthumb').show();
	});

	$('.background, #popover .close').click( function() {
		$('.mapthumb').attr('class', 'mapthumb');
		$('#popover, .mapthumb:not(.single)').hide();
		clearTerms();
	});

	$('.background').click( function() { $('.mapthumb:not(.single)').hide(); });

});