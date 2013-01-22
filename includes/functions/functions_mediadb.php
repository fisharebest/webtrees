<?php
// Various functions used by the media DB interface
//
// webtrees: Web based Family History software
// Copyright (C) 2013 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009 PGV Development Team.  All rights reserved.
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

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

//-- Setup array of media types
$MEDIATYPE = array("a11", "acb", "adc", "adf", "afm", "ai", "aiff", "aif", "amg", "anm", "ans", "apd", "asf", "au", "avi", "awm", "bga", "bmp", "bob", "bpt", "bw", "cal", "cel", "cdr", "cgm", "cmp", "cmv", "cmx", "cpi", "cur", "cut", "cvs", "cwk", "dcs", "dib", "dmf", "dng", "doc", "dsm", "dxf", "dwg", "emf", "enc", "eps", "fac", "fax", "fit", "fla", "flc", "fli", "fpx", "ftk", "ged", "gif", "gmf", "hdf", "iax", "ica", "icb", "ico", "idw", "iff", "img", "jbg", "jbig", "jfif", "jpe", "jpeg", "jp2", "jpg", "jtf", "jtp", "lwf", "mac", "mid", "midi", "miff", "mki", "mmm", ".mod", "mov", "mp2", "mp3", "mpg", "mpt", "msk", "msp", "mus", "mvi", "nap", "ogg", "pal", "pbm", "pcc", "pcd", "pcf", "pct", "pcx", "pdd", "pdf", "pfr", "pgm", "pic", "pict", "pk", "pm3", "pm4", "pm5", "png", "ppm", "ppt", "ps", "psd", "psp", "pxr", "qt", "qxd", "ras", "rgb", "rgba", "rif", "rip", "rla", "rle", "rpf", "rtf", "scr", "sdc", "sdd", "sdw", "sgi", "sid", "sng", "swf", "tga", "tiff", "tif", "txt", "text", "tub", "ul", "vda", "vis", "vob", "vpg", "vst", "wav", "wdb", "win", "wk1", "wks", "wmf", "wmv", "wpd", "wxf", "wp4", "wp5", "wp6", "wpg", "wpp", "xbm", "xls", "xpm", "xwd", "yuv", "zgm");

// looks in both the standard and protected media directories
function findImageSize($file) {
	if (strtolower(substr($file, 0, 7)) == "http://")
		$file = "http://" . rawurlencode(substr($file, 7));
	$imgsize = @getimagesize($file);
	if (!$imgsize) {
		$imgsize = @getimagesize(get_media_firewall_path($file));
	}
	if (!$imgsize) {
		$imgsize[0] = 300;
		$imgsize[1] = 300;
		$imgsize[2] = false;
	}
	return $imgsize;
}

//returns an array of rows from the database containing the Person ID’s for the people associated with this picture
function get_media_relations($mid) {
	global $medialist;

	//-- check in the medialist cache first
	$firstChar = substr($mid, 0, 1);
	$restChar = substr($mid, 1);
	if (is_numeric($firstChar)) {
		$firstChar = "";
		$restChar = $mid;
	}
	$keyMediaList = $firstChar . substr("000000" . $restChar, -6) . "_" . WT_GED_ID;
	if (isset ($medialist[$keyMediaList]['LINKS'])) {
		return $medialist[$keyMediaList]['LINKS'];
	}

	$media = array();
		foreach (fetch_linked_indi($mid, 'OBJE', WT_GED_ID) as $indi) {
			if ($mid!=$indi->getXref()) {
				$media[$indi->getXref()]='INDI';
			}
		}
		foreach (fetch_linked_fam($mid, 'OBJE', WT_GED_ID) as $fam) {
			if ($mid!=$fam->getXref()) {
				$media[$fam->getXref()]='FAM';
			}
		}
		foreach (fetch_linked_sour($mid, 'OBJE', WT_GED_ID) as $sour) {
			if ($mid!=$sour->getXref()) {
				$media[$sour->getXref()]='SOUR';
			}
		}
	$medialist[$keyMediaList]['LINKS'] = $media;
	return $media;
}

// checks whether a media file exists.
// returns 1 for external media
// returns 2 if it was found in the standard folder
// returns 3 if it was found in the media firewall folder
// returns false if not found
function media_exists($filename) {
	if (empty($filename)) { return false; }
	if (isFileExternal($filename)) { return 1; }
	if (file_exists($filename)) { return 2; }
	if (file_exists(get_media_firewall_path($filename))) { return 3; }
	return false;
}

// returns size of file.  looks in both the standard and protected media directories
function media_filesize($filename) {
	if (file_exists($filename)) { return filesize($filename); }
	if (file_exists(get_media_firewall_path($filename))) { return filesize(get_media_firewall_path($filename)); }
	return;
}

// pass in the standard media folder
// returns protected media folder
// strips off any “../” which may be configured in your MEDIA_DIRECTORY variable
function get_media_firewall_path($path) {
	return WT_DATA_DIR . $path;
}

// recursively make directories
// taken from http://us3.php.net/manual/en/function.mkdir.php#60861
function mkdirs($dir, $mode = WT_PERM_EXE, $recursive = true) {
	if (is_null($dir) || $dir==="") {
		return FALSE;
	}
	if (is_dir($dir) || $dir==="/") {
		return TRUE;
	}
	if (mkdirs(dirname($dir), $mode, $recursive)) {
		return mkdir($dir, $mode);
	}
	return FALSE;
}

// converts raw values from php.ini file into bytes
// from http://www.php.net/manual/en/function.ini-get.php
function return_bytes($val) {
	if (!$val) {
		// no value was passed in, assume no limit and return -1
		$val = -1;
	}
	$val = trim($val);
	$last = strtolower($val{strlen($val)-1});
	switch($last) {
		case 'g': $val *= 1024;  // fallthrough
		case 'm': $val *= 1024;  // fallthrough
		case 'k': $val *= 1024;
	}
	return $val;
}

// pass in the full path to an image, returns string with size/height/width/bits/channels
function getImageInfoForLog($filename) {
	$filesize = sprintf("%.2f", filesize($filename)/1024);
	$imgsize = @getimagesize($filename);
	$strinfo = $filesize."kb ";
	if (is_array($imgsize)) { $strinfo .= @$imgsize[0].' × '.@$imgsize[1]." ".@$imgsize['bits']." bits ".@$imgsize['channels']. " channels"; }
	return ($strinfo);
}

// attempts to determine whether there is enough memory to load a particular image
function hasMemoryForImage($serverFilename, $debug_verboseLogging=false) {
	// find out how much total memory this script can access
	$memoryAvailable = return_bytes(@ini_get('memory_limit'));
	// if memory is unlimited, it will return -1 and we don’t need to worry about it
	if ($memoryAvailable == -1) return true;

	// find out how much memory we are already using
	$memoryUsed=memory_get_usage();

	$imgsize = @getimagesize($serverFilename);
	// find out how much memory this image needs for processing, probably only works for jpegs
	// from comments on http://www.php.net/imagecreatefromjpeg
	if (is_array($imgsize) && isset($imgsize['bits']) && (isset($imgsize['channels']))) {
		$memoryNeeded = Round(($imgsize[0] * $imgsize[1] * $imgsize['bits'] * $imgsize['channels'] / 8 + Pow(2, 16)) * 1.65);
		$memorySpare = $memoryAvailable - $memoryUsed - $memoryNeeded;
		if ($memorySpare > 0) {
			// we have enough memory to load this file
			if ($debug_verboseLogging) AddToLog("Media: >about to load< file >".$serverFilename."< (".getImageInfoForLog($serverFilename).") memory avail: ".$memoryAvailable." used: ".$memoryUsed." needed: ".$memoryNeeded." spare: ".$memorySpare, 'media');
			return true;
		} else {
			// not enough memory to load this file
			AddToLog("Media: >image too large to load< file >".$serverFilename."< (".getImageInfoForLog($serverFilename).") memory avail: ".$memoryAvailable." used: ".$memoryUsed." needed: ".$memoryNeeded." spare: ".$memorySpare, 'media');
			return false;
		}
	} else {
		// assume there is enough memory
		// TODO find out how to check memory needs for gif and png
		return true;
	}
}
