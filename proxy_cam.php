<?php require_once('core.php'); require_once('core_motion.php');

$camurl=urlOfCam($_REQUEST['id']);

$boundary="\n--";
$font=5;
$f = fopen($camurl, "r");
if (!$f) {
	// Failed opening the socket => Create error image
	$logo = imagecreatefromgif( "img/offline.gif" );
	imagealphablending($logo, true);
	$color_text = imagecolorallocate( $logo, 255, 0, 0 );
	$color_bg = imagecolorallocate( $logo, 0, 0, 0 );
	$logo_w = imagesx( $logo );
	
	// Write error message
	$msg = "Error accessing $camurl";
	imagestring( $logo, $font, $logo_w/2 - (imagefontwidth($font) * strlen($msg))/2 -1, 20 -1, $msg, $color_bg );
	imagestring( $logo, $font, $logo_w/2 - (imagefontwidth($font) * strlen($msg))/2 +1, 20 -1, $msg, $color_bg );
	imagestring( $logo, $font, $logo_w/2 - (imagefontwidth($font) * strlen($msg))/2 -1, 20 +1, $msg, $color_bg );
	imagestring( $logo, $font, $logo_w/2 - (imagefontwidth($font) * strlen($msg))/2 +1, 20 +1, $msg, $color_bg );
	imagestring( $logo, $font, $logo_w/2 - (imagefontwidth($font) * strlen($msg))/2 , 20, $msg, $color_text );

	// Write date/time
	$date = date( 'Y-m-d-H:i:s');
	imagestring( $logo, $font, $logo_w/2 - (imagefontwidth($font) * strlen($date))/2 , 57, $date, $color_text );
	$date = date( 'O T');
	imagestring( $logo, $font, $logo_w/2 - (imagefontwidth($font) * strlen($date))/2 , 59 + imagefontheight($font), $date, $color_text );

	// Output image
	header( "Content-type: image/gif" );
	imagegif( $logo );
	imagedestroy( $logo );
} else {
	// URL OK => Transfer the image...
	while (substr_count($r, "Content-Length") != 2) $r.=fread($f, 512);

	// Get the content length
	$start = strpos($r, "Content-Length") +15;
	$end   = strpos($r, "\n", $start);
	$length = intval(substr($r, $start, $end - $start));

	// Content starts after CR-LF
	$start = strpos($r, "\r\n", $end) +2;
	$frame = substr($r, $start, $length);

	// Output frame
	header("Content-type: image/jpeg");
	echo $frame;
}

fclose($f);

?>