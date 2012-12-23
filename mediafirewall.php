<?php
// Media Firewall
// Called when a 404 error occurs in the media directory
// Serves images from the index directory
//
// webtrees: Web based Family History software
// Copyright (C) 2012 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//
// $Id$

define('WT_SCRIPT_NAME', 'mediafirewall.php');
require './includes/session.php';

Zend_Session::writeClose();

$mid   = safe_GET_xref('mid');
$thumb = safe_GET_bool('thumb');
$media = WT_Media::getInstance($mid);

$debug_mediafirewall   = 0; // set to 1 if you want to see media firewall values displayed instead of images
$debug_watermark       = 0; // set to 1 if you want to see error messages from the watermark module instead of broken images
$debug_forceImageRegen = 0; // set to 1 if you want to force an image to be regenerated (for debugging only)
$debug_verboseLogging  = 0; // set to 1 for extra logging details

// Send a "Not found" error as an image
function send404AndExit() {
	$error = WT_I18N::translate('The media file was not found in this family tree');

	$error = reverseText($error);
	// width of image is based on the number of characters
	$width = (utf8_strlen($error)) * 6.5;
	$height = 60;
	$im  = imagecreatetruecolor($width, $height);  /* Create a black image */
	$bgc = imagecolorallocate($im, 255, 255, 255); /* set background color */
	$tc  = imagecolorallocate($im, 0, 0, 0);       /* set text color */
	imagefilledrectangle($im, 2, 2, $width-4, $height-4, $bgc); /* create a rectangle, leaving 2 px border */
	imagestring($im, 2, 5, 5, $error, $tc);

	header('HTTP/1.0 404 Not Found');
	header('Status: 404 Not Found');
	header('Content-Type: image/png');
	imagepng($im);
	imagedestroy($im);
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
	$word1_text   = WT_TREE_TITLE;
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
			$font = 'DejaVuSans.ttf'; // this font ships with webtrees
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
	AddToLog("Media Firewall error: >".$errstr."< in file >".$serverFilename."< (".getImageInfoForLog($serverFilename).")", 'error');

	// change value of useTTF to false so the fallback watermarking can be used.
	$useTTF = false;
	return true;
}

// ******************************************************
// start processing here

// this needs to be a global variable so imagettftextErrorHandler can set it
$useTTF = function_exists('imagettftext');

// Media missing/private?
if (!$media || !$media->canDisplayDetails()) {
	send404AndExit();
}

$which = $thumb ? 'thumb' : 'main';
$serverFilename = $media->getServerFilename($which);

$imgsize = $media->getImageAttributes($which);
if (!file_exists($serverFilename)) {
	// the requested file MAY be in the gedcom, but it does NOT exist on the server.  bail.
	// Note: the 404 error status is still in effect.
	if (!$debug_mediafirewall) send404AndExit();
}

$protocol = $_SERVER["SERVER_PROTOCOL"];  // determine if we are using HTTP/1.0 or HTTP/1.1
$filetime = $media->getFiletime($which);
$filetimeHeader = gmdate("D, d M Y H:i:s", $filetime).' GMT';
$expireOffset = 3600 * 24;  // tell browser to cache this image for 24 hours
if (safe_GET('cb')) $expireOffset = $expireOffset * 7; // if cb parameter was sent, cache for 7 days 
$expireHeader = gmdate("D, d M Y H:i:s", WT_TIMESTAMP + $expireOffset) . " GMT";

$type = isImageTypeSupported($imgsize['ext']);
$usewatermark = false;
// if this image supports watermarks and the watermark module is intalled...
if ($type && function_exists("applyWatermark")) {
	// if this is not a thumbnail, or WATERMARK_THUMB is true
	if (($which=='main') || $WATERMARK_THUMB ) {
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

$etag = $media->getEtag($which);
$mimetype = $imgsize['mime'];
$disposition = 'inline';
if (safe_GET('dl')) {
	// if user requested to download the file, adjust headers accordingly
	$mimetype = 'application/octet-stream';
	$disposition = 'attachment';
}

// parse IF_MODIFIED_SINCE header from client
$if_modified_since = 'x';
if (@$_SERVER["HTTP_IF_MODIFIED_SINCE"]) {
	$if_modified_since = preg_replace('/;.*$/', '', $_SERVER["HTTP_IF_MODIFIED_SINCE"]);
}

// parse IF_NONE_MATCH header from client
$if_none_match = 'x';
if (@$_SERVER["HTTP_IF_NONE_MATCH"]) {
	$if_none_match = str_replace("\"", "", $_SERVER["HTTP_IF_NONE_MATCH"]);
}

if ($debug_mediafirewall) {
	// this is for debugging the media firewall
	header("Last-Modified: " . $filetimeHeader);
	header('ETag: "'.$etag.'"');

	echo  '<table border="1">';
	echo  '<tr><td>GEDCOM</td><td>', WT_GEDCOM, '</td><td>&nbsp;</td></tr>';
	echo  '<tr><td>MEDIA_DIRECTORY_LEVELS</td><td>', $MEDIA_DIRECTORY_LEVELS, '</td><td>&nbsp;</td></tr>';
	echo  '<tr><td>$mid</td><td>', $mid, '</td><td>&nbsp;</td></tr>';
	echo  '<tr><td>Requested URL</td><td>', urldecode($_SERVER['REQUEST_URI']), '</td><td>&nbsp;</td></tr>';
	echo  '<tr><td>serverFilename</td><td>', $serverFilename, '</td><td>&nbsp;</td></tr>';
	echo  '<tr><td>media->getFilename()</td><td>', $media->getFilename(), '</td><td>this is direct from the gedcom</td></tr>';
	echo  '<tr><td>media->getLocalFilename()</td><td>', $media->getLocalFilename(), '</td><td></td></tr>';
	echo  '<tr><td>media->getServerFilename()</td><td>', $media->getServerFilename(), '</td><td></td></tr>';
	echo  '<tr><td>media->fileExists()</td><td>', $media->fileExists(), '</td><td></td></tr>';
	echo  '<tr><td>mimetype</td><td>', $mimetype, '</td><td>&nbsp;</td></tr>';
	echo  '<tr><td>disposition</td><td>', $disposition, '</td><td>&nbsp;</td></tr>';
	echo  '<tr><td>media->getFilesize()</td><td>', $media->getFilesize(), '</td><td>cannot use this</td></tr>';
	echo  '<tr><td>filesize</td><td>', @filesize($serverFilename), '</td><td>this is right</td></tr>';
	echo  '<tr><td>media->getThumbnail()</td><td>', $media->getThumbnail(), '</td><td>&nbsp;</td></tr>';
	echo  '<tr><td>media->canDisplayDetails()</td><td>', $media->canDisplayDetails(), '</td><td>&nbsp;</td></tr>';
	echo  '<tr><td>media->getFullName()</td><td>', $media->getFullName(), '</td><td>&nbsp;</td></tr>';
	echo  '<tr><td>basename($serverFilename)</td><td>', basename($serverFilename), '</td><td>&nbsp;</td></tr>';
	echo  '<tr><td>filetime</td><td>', $filetime, '</td><td>&nbsp;</td></tr>';
	echo  '<tr><td>filetimeHeader</td><td>', $filetimeHeader, '</td><td>&nbsp;</td></tr>';
	echo  '<tr><td>if_modified_since</td><td>', $if_modified_since, '</td><td>&nbsp;</td></tr>';
	echo  '<tr><td>if_none_match</td><td>', $if_none_match, '</td><td>&nbsp;</td></tr>';
	echo  '<tr><td>etag</td><td>', $etag, '</td><td>&nbsp;</td></tr>';
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
	print_r ($media);
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

// send headers for the image
if (!$debug_watermark) {
	header("Content-Type: " . $mimetype);
}

if ($generatewatermark) {
	// generate the watermarked image
	$imCreateFunc = 'imagecreatefrom'.$type;
	$im = @$imCreateFunc($serverFilename);

	if ($im) {
		if ($debug_verboseLogging) AddToLog("Media Firewall log: >about to watermark< file >".$serverFilename."< (".getImageInfoForLog($serverFilename).") memory used: ".memory_get_usage(), 'media');
		$im = applyWatermark($im);
		if ($debug_verboseLogging) AddToLog("Media Firewall log: >watermark complete< file >".$serverFilename."< (".getImageInfoForLog($serverFilename).") memory used: ".memory_get_usage(), 'media');

		$imSendFunc = 'image'.$type;
		// save the image, if preferences allow
		if ((($which=='thumb') && $SAVE_WATERMARK_THUMB) || (($which=='main') && $SAVE_WATERMARK_IMAGE)) {
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

		if ($debug_verboseLogging) AddToLog("Media Firewall log: >done with < file >".$serverFilename."< (".getImageInfoForLog($serverFilename).") memory used: ".memory_get_usage(), 'media');
		exit;

	} else {
		// this image is defective.  log it
		AddToLog("Media Firewall error: >".WT_I18N::translate('This media file is broken and cannot be watermarked')."< in file >".$serverFilename."< (".getImageInfoForLog($serverFilename).") memory used: ".memory_get_usage(), 'media');

		// set usewatermark to false so image will simply be passed through below
		$usewatermark = false;
	}
}

// pass the image through without manipulating it

if ($usewatermark) {
	// the stored watermarked image is good, lets use it
	$serverFilename = $watermarkfile;
}

// determine filesize of image (could be original or watermarked version)
$filesize = filesize($serverFilename);

// set content-length header, send file
header("Content-Length: " . $filesize);

// Some servers disable fpassthru() and readfile()
if (function_exists('readfile')) {
	readfile($serverFilename);
} else {
	$fp=fopen($serverFilename, 'rb');
	if (function_exists('fpassthru')) {
		fpassthru($fp);
	} else {
		while (!feof($fp)) {
			echo fread($fp, 65536);
		}
	}
	fclose($fp);
}
