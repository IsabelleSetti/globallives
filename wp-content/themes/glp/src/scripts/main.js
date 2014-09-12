$(function () {

	$('a:has(img)').addClass('image-link');
	same_height('#nav-modules .widget-inner');
	$('input.copyable').click(function(){ $(this).select(); });

/* Functions */

	function set_background( src, arg ) {
		var fade_from = 'rgba(0,0,0,0)',
			fade_to = 'rgba(0,0,0,0)',
			bg = new Image();

		if (arg) {
			fade_from = arg.from ? arg.from : fade_from;
			fade_to = arg.to ? arg.to : fade_to;
		}

		bg.src = src;
		bg.onload = function() {
			var gradient = '('+fade_from+' 75%, '+fade_to+' 100%)';
			var bg_url = 'url('+this.src+')';
			if (bg.src) {
				$('#wrap').css('background-image', '-webkit-linear-gradient' + gradient + ', ' + bg_url);
				$('#wrap').css('background-image', '-moz-linear-gradient' + gradient + ', ' + bg_url);
				$('#wrap').css('background-image', 'linear-gradient' + gradient + ', ' + bg_url);
			}
		};
	}

	function set_stage( post_id ) {
		$('#stage').fadeOut('slow').load(
			glpAjax.ajaxurl,
			{ action: 'get_participant_summary', post_id: post_id },
			function() {
				$('#stage').fadeIn('slow');
				$(window).trigger("setup_players");
				reinit_addthis();
			}
		);
	}

	function show_mapthumb( i ) {
		$('.mapthumb').hide();
		$('#mapthumb-'+i).show();
	}

	function same_height( group ) {
		var resizeTimer;
		$(window).resize(function() {
			clearTimeout(resizeTimer);
			resizeTimer = setTimeout(function() {
				$(group).height('auto');
				if ($(window).width() > 768) {
					var tallest = 0;
					$(group).each(function() {
						var thisHeight = $(this).height();
						if (thisHeight > tallest) { tallest = thisHeight; }
					});
					$(group).height(tallest);
				}
			}, 250);
		});
		$(window).resize();
	}

	function reinit_addthis() {
		var addthis_url = "//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-510832576c1fd9d6";
		if (window.addthis) {
			window.addthis = null;
			window._adr = null;
			window._atc = null;
			window._atd = null;
			window._ate = null;
			window._atr = null;
			window._atw = null;
		}
		$.getScript( addthis_url );
	}

/* Banner Modals */

	function popModal(el) {
		$('.modal').modal('hide');
		$(el).modal('show');
	}

	$('#register-tab, .register-toggle').click(function(ev){ ev.preventDefault(); popModal('#modal-register'); });
	$('#login-tab, .login-toggle').click(function(ev){ ev.preventDefault(); popModal('#modal-login'); });

/* Register Modal */

	$('#registerform input[type="submit"]').click(function (ev) {
		ev.preventDefault();

		var form = $('#registerform'),
			user_email = form.find('#user_email').val(),
			user_login = form.find('#user_login').val(),
			user_pass = form.find('#user_pass').val(),
			user_nonce = form.find('#user_nonce').val(),
			redirect_to = form.find('#redirect_to').val();

		form.find('.alert').hide();

		$.post(glpAjax.ajaxurl, {
			action: 'glp_register_user',
			user_email: user_email,
			user_login: user_login,
			user_pass: user_pass,
			user_nonce: user_nonce
		}, function (res) {
			if (res && res === '1') {
				window.location = redirect_to;
			} else {
				form.find('.alert').html(res).show();
			}
		});

	});

/* Front Page */

	if ($('#front-page').length) { // Make sure we're on the homepage

		$('#featured-carousel').carousel('pause');
		$('#featured-carousel').bind('slide.bs.carousel', function () {
			$('#featured-carousel').css('overflow', 'hidden');
		});
		$('#featured-carousel').bind('slid.bs.carousel', function () {
			$('#featured-carousel').css('overflow', 'visible');
		});

		$('#nav-featured .participant-thumbnail').popover();
		$('#nav-featured .participant-thumbnail').click(function () {
			$('.home-thumbnail, .participant-thumbnail').removeClass('active');
			$(this).addClass('active');
			$('#home').fadeOut('slow');
			set_stage($(this).data('id'));
		});
		$('#nav-featured .home-thumbnail').click(function () {
			$('#stage').fadeOut('slow',function() {
				$('.participant-thumbnail').removeClass('active');
				$('.home-thumbnail').addClass('active');
				$('#home').fadeIn('slow');
			});
		});
	}

/* Explore the Collection */

	function toggle_view( view ) {
		$('.view').slideUp(500,function() {
			$(view).delay(700).slideDown(500);
			$('.btn-'+view.substring(1)).addClass('active').siblings().removeClass('active');
		});
		window.location.hash = view;
	}

	if ($('body.page-explore').length) { // Make sure we're on Explore the Collection

		var view = window.location.hash;
		if (view === '#mapview' || view === '#gridview') {
			toggle_view(view);
		}

		$('.btn-mapview').click(function() { toggle_view('#mapview'); });
		$('.btn-gridview').click(function() { toggle_view('#gridview'); });

		$('#nav-explore input, #nav-explore select').change(function() {
			$(participants).each(function() {
				this.filtered = false;

				if ($('select[name=series]').val() && $('select[name=series]').val() !== "All" && $.inArray($('select[name=series]').val(),this.series) == -1) { this.filtered = true; }
				if ($('select[name=gender]').val() !== "All" && this.gender !== $('select[name=gender]').val() ) { this.filtered = true; }
				if ($('select[name=income]').val() !== "All" && this.income !== $('select[name=income]').val() ) { this.filtered = true; }
				if ($('select[name=age]').val()    !== "All" && this.age    !== $('select[name=age]').val() )    { this.filtered = true; }
			});
			if ($('input[name=proposed]:checked').val()) {
				$('.proposed').removeClass('hide');
				d3.selectAll('.marker.proposed').style('opacity',1);
			} else {
				$('.proposed').addClass('hide');
				d3.selectAll('.marker.proposed').style('opacity',0);
			}

			filterParticipants();
			return false;
		});
	}

	if ($('#nav-themes').length) { // Make sure the Themes navbar is on the page

		$('#nav-themes li').hover(
			function() {
				$(this).siblings().find('.theme-link').hide();
				$(this).find('.theme-link').slideDown();
			},
			function() {
				$(this).children('.theme-link').slideUp();
			}
		);
		$('#nav-themes li').click(function() {
			var theme = $(this).attr('data-term');
			$(participants).each(function() {
				this.filteredByTheme = false;

				if (theme && $.inArray(theme,this.themes) == -1) {
					this.filteredByTheme = true;
				}

			});

			filterParticipants();
			$(this).addClass('active').siblings().removeClass('active');
		});

		$('#nav-themes').find('.thumbnails').cycle({timeout: 250, speed: 250});

		var filterParticipants = function () {
			$(participants).each(function() {
				if (this.filtered === true || this.filteredByTheme === true) {
					$('#participant-' + this.id).addClass('filtered');
					d3.selectAll('#marker-'+this.id).classed('filtered',true);
				} else {
					$('#marker-' + this.id + ', #participant-' + this.id).removeClass('filtered');
					d3.selectAll('#marker-'+this.id).classed('filtered',false);
				}
			});
		};
	}

/* Profiles */

	var checkComplete = function () {
		var modal = $(this).parents('.modal'),
			incomplete = false;
		modal.find('input[required]').each(function () {
			if ($(this).val() === '') { incomplete = true; }
		});
		modal.find('.btn').attr('disabled', incomplete).toggleClass('disabled', incomplete);
	};
	$('#form-profile input').on('blur change', checkComplete);

	$('#form-profile .next').click(function () {
		var nextModal = $(this).parents('.modal').attr('data-next');
		$('.modal').modal('hide');
		$('#'+nextModal).modal('show');
	});

	$('#form-profile #avatar_upload_btn').click(function (ev) {
		ev.preventDefault();
		console.log('!');
		// $('#user_avatar').click();
	});

	$('#form-profile #add-language-btn').click(function () {
		var addedLanguage = $('#add-language').val(),
			slug = addedLanguage.toLowerCase().replace(/[^\w]+/g,'-') + '-name';
		$('#form-profile #available-languages').append('<div class="checkbox"><label><input id="' + slug + '" type="checkbox" name="user_languages[][language_name]" value="' + addedLanguage + '"> ' + addedLanguage + '</label></div>');
		$('#add-language').val('');
		$('#form-profile #available-languages #' + slug).click();
	});
	$('#form-profile #available-languages').on('click', 'input', function () {
		var target = $(this),
			slug = target.val().toLowerCase().replace(/[^\w]+/g,'-');
		if (target.is(':checked')) {
			$('#form-profile #spoken-languages').append('<label class="select inline" id="' + slug + '">' + target.val() + ' <select name="user_languages[][language_level]"><option value="Native">Native</option><option value="Professional">Professional</option><option value="Near Native">Near Native</option><option value="Advanced">Advanced</option><option value="Intermediate">Intermediate</option><option value="Basic">Basic</option></select></label>');
		}
		else { $('#form-profile #spoken-languages').find('#' + slug).remove(); }
	});

	$('.library-participant-header h4').click(function(ev) {
		$(ev.target).parents('.library-participant').toggleClass('open');
	});

	$('.library-participant .toggle-meta').click(function(ev) {
		ev.preventDefault();
		var meta = $(ev.target).parents('.library-participant').find('.participant-meta'),
			hidden = meta.hasClass('hide');
		meta.toggleClass('hide');
		$(ev.target).html(function () { return hidden ? 'Hide info' : 'Show info'; });
	});

	$('.library-filters .filter').click(function () {
		if ($('.library-filters .filter').length === $('.library-filters .filter.active').length) {
			$(this).siblings('.filter').removeClass('active');
		} else {
			$(this).toggleClass('active');
		}
		filterClips();
	});
	$('.library-filters .clear-filters').click(function () {
		$('.library-filters .filter').addClass('active');
		filterClips();
	});
	function filterClips() {
		var tags = [];
		$('.library-filters .filter.active').each(function () {
			tags.push($(this).data('tag'));
		});
		$('.library-clip').hide();
		$.each(tags, function (i, tag) {
			$('.library-clip.'+tag).show();
		});
	}

/* Blog */

	if ($('.blog').length) { // Make sure we're on the blog page
		var bg = $('.blog .post').first().data('bg');
		if (bg) { set_background( bg, {to: '#262626'} ); }
		$('.past-posts .post').each(function() {
			var bg = $(this).data('bg');
			if (bg) { $(this).css('background-image', 'url('+bg+')'); }
		});
	}

/* Events */

	if ($('.events-list').length) { // Make sure we're on the events page
		$('.tribe-events-event').each(function() {
			var bg = $(this).data('bg');
			if (bg) { $(this).css('background-image', 'url('+bg+')'); }
		});

	}

/* Search */

	$('body#search .filter-group input').change(function() {
		var unfiltered = $('.result');
		$('.filter-group').each(function () {
			var group = [];
			$(this).find(':checked').each(function () {
				var filter = $(this).attr('name'),
					value = $(this).val();
				switch (filter) {
					case 'post_type':
						$.merge(group, $('.result-' + value));
						break;
					case 'theme':
						$.merge(group, $('.result:not(.result-participant), .theme-' + value));
						break;
					default:
						$.merge(group, $('.result:not(.result-participant), .result[data-' + filter + '="' + value + '"]'));
				}
			});
			unfiltered = $.grep(unfiltered, function (result) { return $.inArray(result, group) > -1; });
		});
		$('.result').hide();
		$(unfiltered).show();
	});

	$('body#search .toggle-clips').click(function (ev) {
		var participant = $(ev.target).parents('.result-participant'),
			participant_id = participant.attr('id').split('-')[1];
		participant.toggleClass('open');
		$('.result-clip[data-participant="'+participant_id+'"]').slideToggle();
	});

/* Series */

	if ($('body.tax-series').length) { // Make sure we're on the Series archive page

		/* Carousel */

		$('.carousel').carousel('pause');
		$('#series-carousel').bind('slide',function(){
			$('#series-carousel').css('overflow','hidden');
		});
		$('#series-carousel').bind('slid',function(){
			$('#series-carousel').css('overflow','visible');
		});

		$('#nav-series .participant-thumbnail').click(function() {
			$('#home').fadeOut('slow');
			set_stage( $(this).data('id') );
		});
		$('#nav-series .home-thumbnail').click(function() {
			$('#stage').fadeOut('slow',function() {
				$('#home').fadeIn('slow');
			});
		});

		/* Map View */

		$('.btn-mapview').click(function() {
			$('#mapview').slideToggle(500);
		});

	}

/* Theme */

	if ($('body.tax-themes').length) { // Make sure we're on the Theme archive page

		$('#theme-select').change(function() {
			window.location = '/themes/' + $(this).val();
		});

	}

/* Participant - Single */

	if ($('body.single-participant').length) { // Make sure we're on the Participant - Single page

		$('#nav-themes').hide();

		$('.participant-detail-map .handle').click(function() {
			$('#mapview, #nav-themes').slideToggle();
			$('.participant-detail-map .handle .btn span').toggle();
		});

		$('.participant-filter-clips a.filter').click(function () {
			$(this).toggleClass('active');
			$('.participant-clip-listing.'+$(this).data('tag')).toggle();
		});

	}

/* Donate Banner */

	if ($('#donate-banner').length) {
		var banner = $('#donate-banner');
		banner.delay(1000).slideDown(2000);
		$('.not-now').click(function(){ banner.slideUp(2000); });
	}

});