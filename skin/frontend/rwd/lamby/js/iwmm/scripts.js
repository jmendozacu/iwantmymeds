function jsEqualHeight(element) {
	var maxHeight = 0;

	jQuery(element).height('');

	jQuery(element).each(function() {
		var height = jQuery(this).height();

		if (height > maxHeight) {
			maxHeight = height;
		}
	});

	jQuery(element).height(maxHeight);
}

jQuery(function() {
	jQuery('.bxslider').bxSlider({
		auto: true,
		controls: false,
		// mode: 'fade',
	});

	jQuery('.weekly_deals .button').on('mouseenter', function() {
		jQuery(this).closest('.weekly_deals').addClass('hover');
	}).on('mouseleave', function() {
		jQuery(this).closest('.weekly_deals').removeClass('hover');
	});

	jQuery('.jsScrollbar').perfectScrollbar({
		suppressScrollX: true
	});

	function jsEqualElements() {
		jsEqualHeight('.jsEqualBestsName');
		jsEqualHeight('.jsEqualBestsPrice');

		jsEqualHeight('.jsEqualRecommendedName');
		jsEqualHeight('.jsEqualRecommendedPrice');

		jsEqualHeight('.jsEqualCatname');
		jsEqualHeight('.jsEqualCatdesc');

		jsEqualHeight('.jsEqualProductName');

		jsEqualHeight('jsEqualRelatedPrice');
		jsEqualHeight('.jsEqualRecommendedName');
	}

	jsEqualElements();

	jQuery(window).resize(function() {
		jsEqualElements();
	});
});
