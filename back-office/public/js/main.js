(function($){

	$('#slider-transportation').owlCarousel({
		loop: true,
		margin: 10,
		nav: true,
		navText: ['<img src="/images/hotsite/slider-arrow-1.png" alt="" />', '<img src="/images/hotsite/slider-arrow-2.png" alt="" />'],
		dots: false,
		autoplay: true,
		items: 1,
		responsive: {
			0: {
				items: 1
			},
			600: {
				items: 1
			},
			1000: {
				items: 1
			}
		}
	})

	$('#know-app').owlCarousel({
		loop: true,
		margin: 10,
		nav: true,
		navText: ['<img src="/images/hotsite/slider-arrow-1.png" alt="" />', '<img src="/images/hotsite/slider-arrow-2.png" alt="" />'],
		dots: false,
		autoplay: true,
		items: 1,
		responsive: {
			0: {
				items: 1
			},
			600: {
				items: 1
			},
			1000: {
				items: 1
			}
		}
	})

})(jQuery);
