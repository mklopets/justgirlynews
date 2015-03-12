var vhpscroll = 10; // vh per scroll

function elementScroll(e) {
	// scroll up
	if (e.originalEvent.detail < 0 || e.originalEvent.wheelDelta > 0) {
		current = $('body').scrollTop()
		x = 100 / vhpscroll
		$('body').scrollTop(current - height / x)
	}

	// scroll down
	else {
		current = $('body').scrollTop()
		x = 100 / vhpscroll
		$('body').scrollTop(current + height / x)
	}

	// prevent default
	return false;
}

$(function() {
	height = $(window).height()
})
$(window).on({
	'DOMMouseScroll mousewheel': elementScroll
});
$(window).resize(function() {
	height = $(window).height()
})