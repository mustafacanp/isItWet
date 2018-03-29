<?php
header('Content-Type: application/json');

if(isset($_GET['coordinates']) && $_GET['coordinates'] !== ''){
	$coordinates = $_GET['coordinates'];
	$response = isWet($coordinates);
} else {
	$response = [
	    	'error'   => true,
	    	'message' => 'Invalid request. Missing the \'coordinates\' parameter.'
	    ];
}

echo json_encode($response);


function isWet($coordinates) {

	$coord = explode(',', $coordinates);
	$lat = $coord[0];
	$lng = $coord[1];

	$GMAPStaticUrl = 'https://maps.googleapis.com/maps/api/staticmap?center='.$lat.','.$lng.'&style=feature:all|element:labels|visibility:off&size=3x3&maptype=roadmap&sensor=false&zoom=23&key=AIzaSyBvgZVUesDFm6FVESzf7QsISehp2i71w-g'; 
/*
	$GMAPStaticUrl = 'https://maps.googleapis.com/maps/api/staticmap?
	center='.$lat.','.$lng.'
	&style=feature:all|element:labels|visibility:off
	&size=5x5
	&maptype=roadmap
	&sensor=false
	&zoom=23
	&key=AIzaSyBvgZVUesDFm6FVESzf7QsISehp2i71w-g';
*/
	//echo $GMAPStaticUrl;

	$chuid = curl_init();
	curl_setopt($chuid, CURLOPT_URL, $GMAPStaticUrl);   
	curl_setopt($chuid, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($chuid, CURLOPT_SSL_VERIFYPEER, FALSE);
	$data = trim(curl_exec($chuid));
	curl_close($chuid);

	$image = imagecreatefromstring($data);


	// this is for debug to print the image
	ob_start();
	imagepng($image);
	$contents =  ob_get_contents();
	ob_end_clean();
	 echo "<img src='data:image/png;base64,".base64_encode($contents)."' />";

	// here is the test : I only test 3 pixels ( enough to avoid rivers ... )
	$hexaColor = imagecolorat($image,0,1);
	$color_tran = imagecolorsforindex($image, $hexaColor);


	imagedestroy($image);
	// var_dump($red,$green,$blue);


	$red = $color_tran['red'];
	$green = $color_tran['green'];
	$blue = $color_tran['blue'];

	$color = $red.','.$green.','.$blue;

	// echo "<div style='width:300px; height:300px; background-color:rgb(".$color.")'></div>";

	if($color == '224,224,224'){
	    $type = [
	    	'error'   => true,
	    	'message' => 'Please try again.'
	    ];
	}
	if($color == '163,204,255') {
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
