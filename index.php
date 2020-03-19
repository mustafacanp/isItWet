<?php
header('Content-Type: application/json');

$API_KEY = "";

function isWet($coordinates) {
	global $API_KEY;

	$coord = explode(',', $coordinates);
	$lat = $coord[0];
	$lng = $coord[1];

	$GMAPStaticUrl = 'https://maps.googleapis.com/maps/api/staticmap?center='.$lat.','.$lng.'&style=feature:all|element:labels|visibility:off&size=5x5&maptype=roadmap&sensor=false&zoom=23&key='.$API_KEY; 

	$chuid = curl_init();
	curl_setopt($chuid, CURLOPT_URL, $GMAPStaticUrl);   
	curl_setopt($chuid, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($chuid, CURLOPT_SSL_VERIFYPEER, FALSE);
	$data = trim(curl_exec($chuid));
	curl_close($chuid);

	$image = imagecreatefromstring($data);

	ob_start();
	imagepng($image);
	$contents =  ob_get_contents();
	ob_end_clean();

	$hexaColor = imagecolorat($image,0,1);
	$color_tran = imagecolorsforindex($image, $hexaColor);

	imagedestroy($image);

	$red = $color_tran['red'];
	$green = $color_tran['green'];
	$blue = $color_tran['blue'];

	$color = $red.','.$green.','.$blue;

	if ($color == '224,224,224'){
	    $type = [
	    	'error'   => true,
	    	'message' => 'Please try again.'
	    ];
	}

	if ($color == '170,218,255') {
	    $type = [
			'latitude'  => (float) $lat,
			'longitude' => (float) $lng,
			'is_wet' => true
	    ];
	} else {
	    $type = [
			'latitude'  => (float) $lat,
			'longitude' => (float) $lng,
			'is_wet' => false
	    ];
	}

	return $type;
}

if (isset($_GET['coordinates']) && $_GET['coordinates'] !== ''){
	$coordinates = $_GET['coordinates'];
	$coordinates = str_replace(" ","",$coordinates);
	$response = isWet($coordinates);
} else {
	$response = [
	    	'error'   => true,
	    	'message' => 'Invalid request. Missing the \'coordinates\' parameter.'
	    ];
}

echo json_encode($response);
?>
