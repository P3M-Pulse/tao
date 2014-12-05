var api;

window.onItemApiReady = function(itemApi){
	initMap();
	$('#validation-trigger').bind('click', onValidateButtonClicked);
	$('#reset-trigger').bind('click', onResetButtonClicked);
	api = itemApi;
	api.getVariable('response', restoreState);
}

function initMap () {
	var latlng = new google.maps.LatLng(40.7584, -73.9761);
    var myOptions = {
      zoom: 14,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      minZoom: 13,
      streetViewControl: false,
      mapTypeControl: false
    };
    window.map = new google.maps.Map(document.getElementById("map"),
        myOptions);
}

function onValidateButtonClicked (e) {
	var response = $('input[name="answer"]:checked').val();
	if (response !== undefined) {
		api.saveResponses({'response' : response});
	    api.setVariable('response', response);
	}
	if ($('#radio4').attr('checked')) {
		api.saveScores({'score': 1});
	} else {
		api.saveScores({'score': 0});
	}
	
	// Tell TAO to go to the next item for this delivery.
	api.finish();
}

function onResetButtonClicked (e) {
	window.map.setCenter(new google.maps.LatLng(40.7584, -73.9761));
	window.map.setZoom(14);
}

function restoreState(selected) {
	$( 'input[name="answer"]' ).each(function( input ) {
		if ($(this).val() === selected) {
			$(this).attr('checked', 'checked');
		}
	});
}
