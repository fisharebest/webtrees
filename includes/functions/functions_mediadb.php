<?php
// Various functions used by the media DB interface
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009 PGV Development Team.
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
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

use WT\Log;

/**
 * Convert raw values from php.ini file into bytes
 *
 * @param string $val
 *
 * @return integer
 */
function return_bytes($val) {
	if (!$val) {
		// no value was passed in, assume no limit and return -1
		$val = -1;
	}
	switch (substr($val, -1)) {
	case 'g':
	case 'G':
		return (int)$val * 1024 * 1024 * 1024;
	case 'm':
	case 'M':
		return (int)$val * 1024 * 1024;
	case 'k':
	case 'K':
		return (int)$val * 1024;
	default:
		return (int)$val;
	}
}

/**
 * Determine whether there is enough memory to load a particular image.
 *
 * @param string $serverFilename
 *
 * @return boolean
 */
function hasMemoryForImage($serverFilename) {
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
		$memoryNeeded = round(($imgsize[0] * $imgsize[1] * $imgsize['bits'] * $imgsize['channels'] / 8 + Pow(2, 16)) * 1.65);
		$memorySpare = $memoryAvailable - $memoryUsed - $memoryNeeded;
		if ($memorySpare > 0) {
			// we have enough memory to load this file
			return true;
		} else {
			// not enough memory to load this file
			$image_info =  sprintf('%.2fKB, %d × %d %d bits %d channels', filesize($serverFilename)/1024, $imgsize[0], $imgsize[1], $imgsize['bits'], $imgsize['channels']);
			Log::addMediaLog('Cannot create thumbnail '.$serverFilename.' ('.$image_info.') memory avail: '.$memoryAvailable.' used: '.$memoryUsed.' needed: '.$memoryNeeded.' spare: '.$memorySpare);
			return false;
		}
	} else {
		// assume there is enough memory
		// TODO find out how to check memory needs for gif and png
		return true;
	}
}
