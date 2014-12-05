$(document).ready(function () {
	init();
});

var startConfigurations = 	[[1,0,2], [2,1,0], [2,0,1], [0,2,1], [0, 0, 0], [1,1,1], 
												[2,2,2], [0, 1, 1], [0, 2, 2] ,[1, 0, 1], [1,0,2],
												[2, 0, 1], [2, 0, 2]];

/*
 * Free Form Item initialization.
 */
var init = function () {
	window.changing = false; // Indicates if the currently selected thing is changing.
	changeButtonState($('.next, .previous', '#validation-trigger'), true); // Make sure that all buttons are disabled until the item is fully loaded.
	var randomConfig = Math.floor(Math.random() * 13);
	var startConfig = startConfigurations[randomConfig];
	
	// Only initialize first image when all are loaded.
	// (uses image preloader jQuery plugin)
	$('.image_selector .thing').imgpreload(function () {
		window.currentThings = [-1, -1, -1]; // We set the current thing out of bounds during image preload.
		window.countThing = $('.image_selector .thing').length / 3; // Global counter of selectabe things in this item.
		$('.next, #validation-trigger').attr('disabled', false); // User is able at first to go only to a next thing or validate immediately.
		changeThing(0, startConfig[0]); // First things are shown on the screen.
		changeThing(1, startConfig[1]);
		changeThing(2, startConfig[2]);
	});
	

	// Next button click event binding.
	$('.next').bind('click', function(e) { 
		onNextClicked(e, getImageSelectorId(e));
	});
	
	// Previous button click event binding.
	$('.previous').bind('click', function(e) {
		onPreviousClicked(e, getImageSelectorId(e));
	});
	
	// Validate button click event binding.
	$('#validation-trigger').bind('click', function(e) {
		onValidateClicked(e);
	});
}

/*
 * Next button click event handler.
 */
var onNextClicked = function (e, index) {
	if (window.currentThings[index] < window.countThing - 1) {
		// Other images are still available by rotating right.
		changeThing(index, 'right');
	}
};

/*
 * Previous button click event handler.
 */
var onPreviousClicked = function (e, index) {
	if (window.currentThings[index] > 0) {
		// Other images are still available by rotating left.
		changeThing(index, 'left');
	}
}

/*
 * This method is invoked when the end-user clicks on the next or the previous button.
 * As a result, a new image illustrating a "thing" is shown on the screen, depending on
 * the value of the dir parameter.
 * 
 * @param index integer the index for the image to change.
 * @param dir string|integer 'left' or 'right' or an integer to directly select an image by index.
 */
var changeThing = function (index, dir) {
	if (!window.changing) { 
		$things = $('.image_selector').eq(index).find('.thing');
		
		// Hide current thing
		window.changing = true;
		$things.eq(window.currentThings[index]).fadeOut(400, function () {
			// Now show the next image to be displayed.
			if (typeof(dir) == 'string') {
				var increment = (dir == 'left') ? (-1) : 1;
				window.currentThings[index] += increment;
			}
			else {
				window.currentThings[index] = dir;
			}
			
			updateNavigation();
			$things.eq(window.currentThings[index]).fadeIn(400);
			
			window.changing = false;
		});	
	}
};

/*
 * This method is invoked after a "thing" has changed on the screen, to check wheter or not
 * Next or Previous buttons have to be disabled.
 */
var updateNavigation = function () {
	$('.image_selector').each(function (i) {
		if (window.currentThings[i] + 1 == window.countThing) {
			// The displayed thing is the last one of the list. Next button should be disabled.
			changeButtonState($(this).find('.previous'), false);
			changeButtonState($(this).find('.next'),true);
		}
		else if (window.currentThings[i] == 0) {
			// The displayed thing is the first one of the list. Previous button should be disabled.
			changeButtonState($(this).find('.previous'), true) ;
			changeButtonState($(this).find('.next'), false);
		}
		else {
			changeButtonState($(this).find('.next, .previous'), false);
		}
	});
};

/*
 * This method allows you to disable or enable a particular button.
 * 
 * @param  $button jQuery The button you want to disable or enable.
 * @param disabled boolean True if you want the button to be disabled. Otherwise, false.
 */
var changeButtonState = function ($button, disabled) {
		
	if (disabled) {
		$button.addClass('disabled');
		$button.attr('disabled', true);
	}	
	else {
		$button.removeClass('disabled');
		$button.removeAttr('disabled');
	}
};

/*
 * Get the index of the "image_selector" element that contains
 * the button element that triggered a particular event.
 *
 * @param e Event The event dispatched by a button contained with an "image_select" element.
 */
var getImageSelectorId = function(e) {
	var targetId = $(e.target).parent().parent().attr('id');
	var len = targetId.length;
	return parseInt(targetId.substring(len - 1, len))  - 1;
}

/*
 * Disable all button in the item.
 */
var freezeGUI = function () {
	$('button').attr('disabled', true).addClass('disabled');
}

/* 
 * This is where we will work together.
 */
var onValidateClicked = function () {
	// Freeze the interface to be sure the respondent does not
	// try to change its anwser during it is sent.
	freezeGUI();
	
	// Item Runtime API calls.
	if (window.currentThings[0] == 0 && window.currentThings[1] == 2 && window.currentThings[2] == 2) {
		// Approve the answer.
		setEndorsment(true);
		
		// Give an appropriate score.
		setScore(1);
	}
	else {
		// Disaprove the answer.
		setEndorsment(false);
		
		// Give an appropriate score.
		setScore(0);
	}
	
	// Alert TAO that the taking of this item is finished.
	finish();
}