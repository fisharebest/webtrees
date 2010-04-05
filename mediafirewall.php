<?php
/**
 * Media Firewall
 * Called when a 404 error occurs in the media directory
 * Serves images from the index directory
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 *
 * @package webtrees
 * @version $Id$
 */

define('WT_SCRIPT_NAME', 'mediafirewall.php');
require './includes/session.php';
require_once WT_ROOT.'includes/controllers/media_ctrl.php';

// We have finished writing to $_SESSION, so release the lock
session_write_close();

$controller = new MediaController();
$controller->init();

$debug_mediafirewall	= 0; 	// set to 1 if you want to see media firewall values displayed instead of images
$debug_watermark		= 0; 	// set to 1 if you want to see error messages from the watermark module instead of broken images
$debug_forceImageRegen	= 0;	// set to 1 if you want to force an image to be regenerated (for debugging only)
$debug_verboseLogging = 0;		// set to 1 for extra logging details


// pass in an image type and an error message
// if the image type is supported:
//   creates an image, adds the text, sends headers, outputs the image and exits the script
// if the image type is not supported:
//   sends html version of error message and exits the script
// basic idea from http://us.php.net/manual/en/function.imagecreatefromjpeg.php
// type  - file extension: JPG, GIF, PNG
// line1 - the error message
// line2 - the media file which caused the error (shown only to admins/editors)
function sendErrorAndExit($type, $line1, $line2 = false) {

	// line2 contains the information that only an admin/editor should see, such as the full path to a file
	if(!WT_USER_CAN_EDIT) {
		$line2 = false;
	}

	// arbitrary maxlen to keep images from getting too wide
	$maxlen = 100;
	$numchars = utf8_strlen($line1);
	if ($numchars > $maxlen) {
		$line1 = utf8_substr($line1, $maxlen);
		$numchars = $maxlen;
	}
	$line1 = reverseText($line1);
	if ($line2) {
		$numchars2 = utf8_strlen($line2);
		if ($numchars2 > $maxlen) {
			$line2 = utf8_substr($line2, $maxlen);
			$numchars2 = $maxlen;
		}
		if ($numchars2 > $numchars) {
			$numchars = $numchars2;
		}
		$line2 = reverseText($line2);
	}

	$type = isImageTypeSupported($type);
	if ( $type ){
		// width of image is based on the number of characters
		$width = ($numchars+1) * 6.5;
		$height = 60;

		$im  = imagecreatetruecolor($width, $height);  /* Create a black image */
		$bgc = imagecolorallocate($im, 255, 255, 255); /* set background color */
		$tc  = imagecolorallocate($im, 0, 0, 0);       /* set text color */
		imagefilledrectangle($im, 2, 2, $width-4, $height-4, $bgc); /* create a rectangle, leaving 2 px border */
		imagestring($im, 2, 5, 5, $line1, $tc);
		if ($line2) {
			imagestring($im, 2, 5, 30, $line2, $tc);
		}

		// if we are using mod rewrite, there will be no error status.  be sure to set it
		header('HTTP/1.0 404 Not Found');
		header('Status: 404 Not Found');
		header('Content-Type: image/'.$type);
		$imSendFunc = 'image'.$type;
		$imSendFunc($im);
		imagedestroy($im);
	} else {
		// output a standard html string
		// if we are using mod rewrite, there will be no error status.  be sure to set it
		header('HTTP/1.0 404 Not Found');
		header('Status: 404 Not Found');
		echo "<html ", i18n::html_markup(), "><body>\n";
		echo "<!-- filler space so IE will display the custom 404 error -->";
		echo "<!-- filler space so IE will display the custom 404 error -->";
		echo "<!-- filler space so IE will display the custom 404 error -->";
		echo "<!-- filler space so IE will display the custom 404 error -->";
		echo "<!-- filler space so IE will display the custom 404 error -->";
		echo "<!-- filler space so IE will display the custom 404 error -->";
		echo "<!-- filler space so IE will display the custom 404 error -->";
		echo "<!-- filler space so IE will display the custom 404 error -->";
		echo "\n<div align=\"center\">", $line1, "</div>\n";
		if ($line2) {
			// line2 comes from url, wrap in PrintReady
			echo "<div align=\"center\">", PrintReady($line2), "</div>\n";
		}
		echo "</body></html>\n";
	}
	exit;
}

// pass in the complete serverpath to an image
// this returns the complete serverpath to be used by the saved watermarked image
// note that each gedcom gets a unique path to store images, this allows each gedcom to have their own watermarking config
function getWatermarkPath ($path) {
	global $MEDIA_DIRECTORY;
	$serverroot = get_media_firewall_path($MEDIA_DIRECTORY);
	$path = str_replace($serverroot, $serverroot . 'watermark/'.WT_GEDCOM.'/', $path);
	return $path;
}



// the media firewall passes in an image
// this function can manipulate the image however it wants
// before returning it back to the media firewall
function applyWatermark($im) {
	// in the future these options will be set in the gedcom configuration area

	// text to watermark with
	$word1_text   = get_gedcom_setting(WT_GED_ID, 'title');
	// maximum font size for "word1" ; will be automaticaly reduced to fit in the image
	$word1_maxsize = 100;
	// rgb color codes for text
	$word1_color  = "0, 0, 0";
	// ttf font file to use. must exist in the includes/fonts/ directory
	$word1_font   = "";
	// vertical position for the text to past; possible values are: top, middle or bottom, across
	$word1_vpos   = "across";
	// horizontal position for the text to past in media file; possible values are: left, right, top2bottom, bottom2top
	// this value is used only if $word1_vpos=across
	$word1_hpos   = "left";

	$word2_text   = $_SERVER["HTTP_HOST"];
	$word2_maxsize = 20;
	$word2_color  = "0, 0, 0";
	$word2_font   = "";
	$word2_vpos   = "top";
	$word2_hpos   = "top2bottom";

	embedText($im, $word1_text, $word1_maxsize, $word1_color, $word1_font, $word1_vpos, $word1_hpos);
	embedText($im, $word2_text, $word2_maxsize, $word2_color, $word2_font, $word2_vpos, $word2_hpos);

	return ($im);
}

function embedText($im, $text, $maxsize, $color, $font, $vpos, $hpos) {
	global $useTTF;

	// there are two ways to embed text with PHP
	// (preferred) using GD and FreeType you can embed text using any True Type font
	// (fall back) if that is not available, you can insert basic monospaced text
	if ($useTTF) {
		// imagettftext is available, make sure the requested font exists
		if (!isset($font)||($font=='')||!file_exists(WT_ROOT.'includes/fonts/'.$font)) {
			$font = 'DejaVuSans.ttf'; // this font ships with PGV
			if (!file_exists(WT_ROOT.'includes/fonts/'.$font)) {
				$useTTF = false;
			}
		}
	}

	# no errors if an invalid color string was passed in, just strange colors
	$col=explode(",", $color);
	$textcolor = @imagecolorallocate($im, $col[0], $col[1], $col[2]);

	// paranoia is good!  make sure all variables have a value
	if (!isset($vpos) || ($vpos!="top" && $vpos!="middle" && $vpos!="bottom" && $vpos!="across")) $vpos = "middle";
	if (($vpos=="across") && (!isset($hpos) || ($hpos!="left" && $hpos!="right" && $hpos!="top2bottom" && $hpos!="bottom2top"))) $hpos = "left";

	// make adjustments to settings that imagestring and imagestringup can't handle
	if (!$useTTF) {
		// imagestringup only writes up, can't use top2bottom
		if ($hpos=="top2bottom") $hpos = "bottom2top";
	}

	$text = reverseText($text);
	$height = imagesy($im);
	$width  = imagesx($im);
	$calc_angle=rad2deg(atan($height/$width));
	$hypoth=$height/sin(deg2rad($calc_angle));

	// vertical and horizontal position of the text
	switch ($vpos) {
		case "top":
			$taille=textlength($maxsize, $width, $text);
			$pos_y=$height*0.15+$taille;
			$pos_x=$width*0.15;
			$rotation=0;
			break;
		case "middle":
			$taille=textlength($maxsize, $width, $text);
			$pos_y=($height+$taille)/2;
			$pos_x=$width*0.15;
			$rotation=0;
			break;
		case "bottom":
			$taille=textlength($maxsize, $width, $text);
			$pos_y=($height*.85-$taille);
			$pos_x=$width*0.15;
			$rotation=0;
			break;
		case "across":
			switch ($hpos) {
				case "left":
				$taille=textlength($maxsize, $hypoth, $text);
				$pos_y=($height*.85-$taille);
				$taille_text=($taille-2)*(utf8_strlen($text));
				$pos_x=$width*0.15;
				$rotation=$calc_angle;
				break;
				case "right":
				$taille=textlength($maxsize, $hypoth, $text);
				$pos_y=($height*.15-$taille);
				$pos_x=$width*0.85;
				$rotation=$calc_angle+180;
				break;
				case "top2bottom":
				$taille=textlength($maxsize, $height, $text);
				$pos_y=($height*.15-$taille);
				$pos_x=($width*.90-$taille);
				$rotation=-90;
				break;
				case "bottom2top":
				$taille=textlength($maxsize, $height, $text);
				$pos_y = $height*0.85;
				$pos_x = $width*0.15;
				$rotation=90;
				break;
			}
			break;
		default:
	}

	// apply the text
	if ($useTTF) {
		// if imagettftext throws errors, catch them with a custom error handler
		set_error_handler("imagettftextErrorHandler");
		imagettftext($im, $taille, $rotation, $pos_x, $pos_y, $textcolor, 'includes/fonts/'.$font, $text);
		restore_error_handler();
	}
	// don't use an 'else' here since imagettftextErrorHandler may have changed the value of $useTTF from true to false
	if (!$useTTF) {
		if ($rotation!=90) {
			imagestring($im, 5, $pos_x, $pos_y, $text, $textcolor);
		} else {
			imagestringup($im, 5, $pos_x, $pos_y, $text, $textcolor);
		}
	}

}

function textlength($t, $mxl, $text) {
	$taille_c = $t;
	$len = utf8_strlen($text);
	while (($taille_c-2)*($len) > $mxl) {
		$taille_c--;
		if ($taille_c == 2) break;
	}
	return ($taille_c);
}

// imagettftext is the function that is most likely to throw an error
// use this custom error handler to catch and log it
function imagettftextErrorHandler($errno, $errstr, $errfile, $errline) {
	global $useTTF, $serverFilename;
	// log the error
	AddToLog("Media Firewall error: >".$errstr."< in file >".$serverFilename."< (".getImageInfoForLog($serverFilename).")" );

	// change value of useTTF to false so the fallback watermarking can be used.
	$useTTF = false;
	return true;
}

// ******************************************************
// start processing here

// to allow watermarking of large images, attempt to disable or raise memory limits
// @ini_set("memory_limit", "-1");
// @ini_set("memory_limit", "64M");

// this needs to be a global variable so imagettftextErrorHandler can set it
$useTTF = (function_exists("imagettftext")) ? true : false;

// get serverfilename from the media controller
$serverFilename = $controller->getServerFilename();
if (!$serverFilename) {
	// either the server is not setting the REQUEST_URI variable as we expect,
	// or the media firewall is being used from outside the media directory
	$requestedfile = ( isset($_SERVER['REQUEST_URI']) ) ? $_SERVER['REQUEST_URI'] : "REQUEST_URI NOT SET";
	$exp = explode("?", $requestedfile);
	$pathinfo = pathinfo($exp[0]);
	$ext = @strtolower($pathinfo['extension']);
	if (!$debug_mediafirewall) sendErrorAndExit($ext, i18n::translate('Error: The Media Firewall was launched from a directory other than the media directory.'), $requestedfile);
}

$isThumb = false;
if (strpos($_SERVER['REQUEST_URI'], '/thumbs/')) {
	// the user requested a thumbnail, but the $controller only knows how to lookup information on the main file
	// display the thumbnail file instead of the main file
	// NOTE: since this script was called when a 404 error occured, we know the requested file
	// does not exist in the main media directory.  just check the media firewall directory
	$serverFilename = get_media_firewall_path($controller->mediaobject->getThumbnail(false));
	$isThumb = true;
}

if (!file_exists($serverFilename)) {
	// the requested file MAY be in the gedcom, but it does NOT exist on the server.  bail.
	// Note: the 404 error status is still in effect.
	if (!$debug_mediafirewall) sendErrorAndExit($controller->mediaobject->getFiletype(), i18n::translate('No Media Found'), $serverFilename);
}

if (empty($controller->pid)) {
	// the requested file IS NOT in the gedcom, but it exists (the check for fileExists was above)
	if (!WT_USER_IS_ADMIN) {
		// only show these files to admin users
		// bail since current user is not admin
		// Note: the 404 error status is still in effect.
//		if (!$debug_mediafirewall) sendErrorAndExit($controller->mediaobject->getFiletype(), i18n::translate('Privacy restrictions prevent you from viewing this item'), $serverFilename);
	}
}

// check PGV permissions
if (!$controller->mediaobject->canDisplayDetails()) {
	// if no permissions, bail
	// Note: the 404 error status is still in effect
	if (!$debug_mediafirewall) sendErrorAndExit($controller->mediaobject->getFiletype(), i18n::translate('Privacy restrictions prevent you from viewing this item'));
}

$protocol = $_SERVER["SERVER_PROTOCOL"];  // determine if we are using HTTP/1.0 or HTTP/1.1
$filetime = @filemtime($serverFilename);
$filetimeHeader = gmdate("D, d M Y H:i:s", $filetime).' GMT';
$expireOffset = 3600 * 24;  // tell browser to cache this image for 24 hours
$expireHeader = gmdate("D, d M Y H:i:s", time() + $expireOffset) . " GMT";

$type = isImageTypeSupported($controller->mediaobject->getFiletype());
$usewatermark = false;
// if this image supports watermarks and the watermark module is intalled...
if ($type && function_exists("applyWatermark")) {
	// if this is not a thumbnail, or WATERMARK_THUMB is true
	if (!$isThumb || $WATERMARK_THUMB ) {
		// if the user's priv's justify it...
		if (WT_USER_ACCESS_LEVEL > $SHOW_NO_WATERMARK ) {
			// add a watermark
			$usewatermark = true;
		}
	}
}

// determine whether we have enough memory to watermark this image
if ($usewatermark) {
	if (!hasMemoryForImage($serverFilename, $debug_verboseLogging)) {
		// not enough memory to watermark this file
		$usewatermark = false;
	}
}

$watermarkfile = "";
$generatewatermark = false;

if ($usewatermark) {
	$watermarkfile = getWatermarkPath($serverFilename);
	if (!file_exists($watermarkfile) || $debug_forceImageRegen) {
		// no saved watermark file exists
		// generate the watermark file
		$generatewatermark = true;
	} else {
		$watermarktime = filemtime($watermarkfile);
		if ($filetime > $watermarktime) {
			// if the original image was updated after the saved file was created
			// generate the watermark file
			$generatewatermark = true;
		}
	}
}

$mimetype = $controller->mediaobject->getMimetype();

// setup the etag.  use enough info so that if anything important changes, the etag won't match
$etag_string = basename($serverFilename).$filetime.WT_USER_ACCESS_LEVEL.$SHOW_NO_WATERMARK;
$etag = dechex(crc32($etag_string));

// parse IF_MODIFIED_SINCE header from client
$if_modified_since = 'x';
if (@$_SERVER["HTTP_IF_MODIFIED_SINCE"]) {
	$if_modified_since = preg_replace('/;.*$/', '', $_SERVER["HTTP_IF_MODIFIED_SINCE"]);
}

// parse IF_NONE_MATCH header from client
$if_none_match = 'x';
if (@$_SERVER["HTTP_IF_NONE_MATCH"]) {
	$if_none_match = str_replace('\"', '', $_SERVER["HTTP_IF_NONE_MATCH"]);
}

if ($debug_mediafirewall) {
	// this is for debugging the media firewall
	header("Last-Modified: " . $filetimeHeader);
	header('ETag: "'.$etag.'"');

	echo  '<table border="1">';
	echo  '<tr><td>GEDCOM</td><td>', WT_GEDCOM, '</td><td>&nbsp;</td></tr>';
	echo  '<tr><td>MEDIA_DIRECTORY_LEVELS</td><td>', $MEDIA_DIRECTORY_LEVELS, '</td><td>&nbsp;</td></tr>';
	echo  '<tr><td>$controller->pid</td><td>', $controller->pid, '</td><td>&nbsp;</td></tr>';
	echo  '<tr><td>Requested URL</td><td>', urldecode($_SERVER['REQUEST_URI']), '</td><td>&nbsp;</td></tr>';
	echo  '<tr><td>serverFilename</td><td>', $serverFilename, '</td><td>&nbsp;</td></tr>';
	echo  '<tr><td>controller->mediaobject->getFilename()</td><td>', $controller->mediaobject->getFilename(), '</td><td>this is direct from the gedcom</td></tr>';
	echo  '<tr><td>controller->mediaobject->getServerFilename()</td><td>', $controller->mediaobject->getServerFilename(), '</td><td></td></tr>';
	echo  '<tr><td>controller->mediaobject->fileExists()</td><td>', $controller->mediaobject->fileExists(), '</td><td></td></tr>';
	echo  '<tr><td>controller->mediaobject->getFiletype()</td><td>', $controller->mediaobject->getFiletype(), '</td><td>&nbsp;</td></tr>';
	echo  '<tr><td>mimetype</td><td>', $mimetype, '</td><td>&nbsp;</td></tr>';
	echo  '<tr><td>controller->mediaobject->getFilesize()</td><td>', $controller->mediaobject->getFilesize(), '</td><td>cannot use this</td></tr>';
	echo  '<tr><td>filesize</td><td>', @filesize($serverFilename), '</td><td>this is right</td></tr>';
	echo  '<tr><td>controller->mediaobject->getThumbnail()</td><td>', $controller->mediaobject->getThumbnail(), '</td><td>&nbsp;</td></tr>';
	echo  '<tr><td>controller->mediaobject->canDisplayDetails()</td><td>', $controller->mediaobject->canDisplayDetails(), '</td><td>&nbsp;</td></tr>';
	echo  '<tr><td>controller->mediaobject->getFullName()</td><td>', $controller->mediaobject->getFullName(), '</td><td>&nbsp;</td></tr>';
	echo  '<tr><td>basename($serverFilename)</td><td>', basename($serverFilename), '</td><td>&nbsp;</td></tr>';
	echo  '<tr><td>filetime</td><td>', $filetime, '</td><td>&nbsp;</td></tr>';
	echo  '<tr><td>filetimeHeader</td><td>', $filetimeHeader, '</td><td>&nbsp;</td></tr>';
	echo  '<tr><td>if_modified_since</td><td>', $if_modified_since, '</td><td>&nbsp;</td></tr>';
	echo  '<tr><td>if_none_match</td><td>', $if_none_match, '</td><td>&nbsp;</td></tr>';
	echo  '<tr><td>etag</td><td>', $etag, '</td><td>&nbsp;</td></tr>';
	echo  '<tr><td>etag_string</td><td>', $etag_string, '</td><td>&nbsp;</td></tr>';
	echo  '<tr><td>expireHeader</td><td>', $expireHeader, '</td><td>&nbsp;</td></tr>';
	echo  '<tr><td>protocol</td><td>', $protocol, '</td><td>&nbsp;</td></tr>';
	echo  '<tr><td>SHOW_NO_WATERMARK</td><td>', $SHOW_NO_WATERMARK, '</td><td>&nbsp;</td></tr>';
	echo  '<tr><td>WT_USER_ACCESS_LEVEL</td><td>', WT_USER_ACCESS_LEVEL, '</td><td>&nbsp;</td></tr>';
	echo  '<tr><td>usewatermark</td><td>', $usewatermark, '</td><td>&nbsp;</td></tr>';
	echo  '<tr><td>generatewatermark</td><td>', $generatewatermark, '</td><td>&nbsp;</td></tr>';
	echo  '<tr><td>watermarkfile</td><td>', $watermarkfile, '</td><td>&nbsp;</td></tr>';
	echo  '<tr><td>type</td><td>', $type, '</td><td>&nbsp;</td></tr>';
	echo  '<tr><td>WATERMARK_THUMB</td><td>', $WATERMARK_THUMB, '</td><td>&nbsp;</td></tr>';
	echo  '<tr><td>SAVE_WATERMARK_THUMB</td><td>', $SAVE_WATERMARK_THUMB, '</td><td>&nbsp;</td></tr>';
	echo  '<tr><td>SAVE_WATERMARK_IMAGE</td><td>', $SAVE_WATERMARK_IMAGE, '</td><td>&nbsp;</td></tr>';
	echo  '</table>';

	echo '<pre>';
	print_r (@getimagesize($serverFilename));
	print_r ($controller->mediaobject);
	print_r (WT_GEDCOM);
	echo '</pre>';

	phpinfo();
	exit;
}
// do the real work here

// add caching headers.  allow browser to cache file, but not proxy
if (!$debug_forceImageRegen) {
	header("Last-Modified: " . $filetimeHeader);
	header('ETag: "'.$etag.'"');
	header("Expires: ".$expireHeader);
	header("Cache-Control: max-age=".$expireOffset.", s-maxage=0, proxy-revalidate");
}

// if this file is already in the user's cache, don't resend it
// first check if the if_modified_since param matches
if (($if_modified_since == $filetimeHeader) && !$debug_forceImageRegen) {
	// then check if the etag matches
	if ($if_none_match == $etag) {
		header($protocol." 304 Not Modified");
		exit;
	}
}

// reset the 404 error
header($protocol." 200 OK");
header("Status: 200 OK");

// send headers for the image
if (!$debug_watermark) {
	header("Content-Type: " . $mimetype);
	header('Content-Disposition: inline; filename="'.basename($serverFilename).'"');
}

if ( $generatewatermark ) {
	// generate the watermarked image
	$imCreateFunc = 'imagecreatefrom'.$type;
	$im = @$imCreateFunc($serverFilename);

	if ($im) {
		if ($debug_verboseLogging) AddToLog("Media Firewall log: >about to watermark< file >".$serverFilename."< (".getImageInfoForLog($serverFilename).") memory used: ".memory_get_usage());
		$im = applyWatermark($im);
		if ($debug_verboseLogging) AddToLog("Media Firewall log: >watermark complete< file >".$serverFilename."< (".getImageInfoForLog($serverFilename).") memory used: ".memory_get_usage());

		$imSendFunc = 'image'.$type;
		// save the image, if preferences allow
		if ( ($isThumb && $SAVE_WATERMARK_THUMB) || (!$isThumb && $SAVE_WATERMARK_IMAGE) ) {
			// make sure the directory exists
			if (!is_dir(dirname($watermarkfile))) {
				mkdirs(dirname($watermarkfile));
			}
			// save the image
			$imSendFunc($im, $watermarkfile);
		}

		// send the image
		$imSendFunc($im);
		imagedestroy($im);

		if ($debug_verboseLogging) AddToLog("Media Firewall log: >done with < file >".$serverFilename."< (".getImageInfoForLog($serverFilename).") memory used: ".memory_get_usage());
		exit;

	} else {
		// this image is defective.  log it
		AddToLog("Media Firewall error: >".i18n::translate('This media file is broken and cannot be watermarked')."< in file >".$serverFilename."< (".getImageInfoForLog($serverFilename).") memory used: ".memory_get_usage());

		// set usewatermark to false so image will simply be passed through below
		$usewatermark = false;
	}
}

// pass the image through without manipulating it

if ( $usewatermark ) {
	// the stored watermarked image is good, lets use it
	$serverFilename = $watermarkfile;
}

// determine filesize of image (could be original or watermarked version)
$filesize = filesize($serverFilename);

// set one more header
header("Content-Length: " . $filesize);
// open the file and send it
$fp = fopen($serverFilename, 'rb');
fpassthru($fp);
exit;

?>
