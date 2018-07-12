<?php
header('Content-Type: application/json');

if(isset($_GET['coordinates']) && $_GET['coordinates'] !== ''){
	$c = $_GET['coordinates'];
	$c = str_replace(' ','',$c);
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

	/* If you want to see you can print image to screen
	ob_start();
	imagepng($pxl);
	$image =  ob_get_contents();
	ob_end_clean();
	echo '<img src='data:image/png;base64,'.base64_encode($image).'' />';
	*/

	$googleWetColor = '170,218,255';
	$googleErrorColor = '224,224,224';

	$hexaColor = imagecolorat($pxl,0,1);
	$color_rgb = imagecolorsforindex($pxl, $hexaColor);
	imagedestroy($pxl);

	$color = $color_rgb['red'].','.$color_rgb['green'].','.$color_rgb['blue'];

	if($color === $googleWetColor){
	    $type = [
	    	'error'   => true,
	    	'message' => 'Google Maps did not respond to your request. Please try again.'
	    ];
	}
	if($color === $googleWetColor) {
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
