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

// looks in both the standard and protected media directories
function findImageSize($file) {
	if (strtolower(substr($file, 0, 7)) == "http://")
		$file = "http://" . rawurlencode(substr($file, 7));
	$imgsize = @getimagesize($file);
	if (!$imgsize) {
		$imgsize[0] = 300;
		$imgsize[1] = 300;
		$imgsize[2] = false;
	}
	return $imgsize;
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
