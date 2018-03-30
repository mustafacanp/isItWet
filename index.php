<?php
header('Content-Type: application/json');

if(isset($_GET['coordinates']) && $_GET['coordinates'] !== ''){
	$c = $_GET['coordinates'];
	$res = isItWet($c);
} else {
	$res = [
	    	'error'   => true,
	    	'message' => 'Invalid request. Missing the \'coordinates\' parameter.'
	    ];
}

echo json_encode($res);


function isItWet($co) {

	$c = explode(',', $co);
	$lat = $c[0];
	$lng = $c[1];

	$gmURL = 'https://maps.googleapis.com/maps/api/staticmap?center='.$lat.','.$lng.'&style=feature:all|element:labels|visibility:off&size=3x3&maptype=roadmap&sensor=false&zoom=23'; 

	$cu = curl_init();
	curl_setopt($cu, CURLOPT_URL, $gmURL);
	curl_setopt($cu, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($cu, CURLOPT_SSL_VERIFYPEER, FALSE);
	$res = trim(curl_exec($cu));
	curl_close($cu);

	$pxl = imagecreatefromstring($res);
	$hexaColor = imagecolorat($pxl,0,1);
	$color_rgb = imagecolorsforindex($pxl, $hexaColor);
	imagedestroy($pxl);

	$color = $color_rgb['red'].','.$color_rgb['green'].','.$color_rgb['blue'];

	if($color === '224,224,224'){
	    $type = [
	    	'error'   => true,
	    	'message' => 'Google Maps did not respond to your request. Please try again.'
	    ];
	}
	if($color === '163,204,255') {
	    $type = [
			'latitude'      => (float) $lat,
			'longitude'      => (float) $lng,
			'is_wet' => true
	    ];
	} else {
	    $type = [
			'latitude'      => (float) $lat,
			'longitude'      => (float) $lng,
			'is_wet' => false
	    ];
	}

	return $type;
}
?>
