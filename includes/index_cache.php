<?php
/**
 * Index caching functions
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2009 PGV Development Team.  All rights reserved.
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
 * @package webtrees
 * @subpackage Display
 * @version $Id$
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('WT_INDEX_CACHE_PHP', '');

/**
 * load a cached block from a file
 * @param array $block	[0]:name of the block to load, [1]:block's configuration
 * @param int $index	An id for this block in the case of multiple instances of the same block on the page
 * @return boolean  returns false if the block could not be loaded from cache
 */
function loadCachedBlock($block, $index) {
	global $WT_BLOCKS, $INDEX_DIRECTORY, $theme_name, $GEDCOM;

	//-- ignore caching when DEBUG is set
	//-- ignore caching for logged in users
	if (WT_DEBUG || WT_USER_ID) {
		return false;
	}

	//-- ignore cache when its life is not configured or when its life is zero
	$cacheLife = $WT_BLOCKS[$block[0]]['config']['cache'];
	if ($cacheLife==0) return false;

	$fname = "{$INDEX_DIRECTORY}/cache/{$theme_name}/".WT_LOCALE."/".WT_GEDCOM."/{$index}_{$block[0]}";
	if (file_exists($fname)) {
		// Check for expired cache (<0: no expiry), 0: immediate, >0: expires in x days)  Zero already checked
		if ($cacheLife > 0) {
			$modtime = filemtime($fname);
			//-- time should start at the beginning of the day
			$modtime = $modtime - (date("G",$modtime)*60*60 + date("i",$modtime)*60 + date("s",$modtime));
			$checktime = ($cacheLife*24*60*60);
			$modtime = $modtime+$checktime;
			if ($modtime<time()) return false;
		}
		return @readfile($fname);
	}
	return false;
}

/**
 * Save a block's content to the cache file
 * @param array $block	[0]:name of the block to save, [1]:block's configuration
 * @param int $index	An id for this block in the case of multiple instances of the same block on the page
 * @param string $content	the actual content to save in the cache
 * @return boolean  returns false if the block could not be saved to cache
 */
function saveCachedBlock($block, $index, $content) {
	global $WT_BLOCKS, $INDEX_DIRECTORY, $theme_name, $GEDCOM;

	//-- ignore caching when DEBUG is set
	//-- ignore caching for logged in users
	if (WT_DEBUG || WT_USER_ID) {
		return false;
	}

	//-- ignore cache when its life is not configured or when its life is zero
	$cacheLife=$WT_BLOCKS[$block[0]]['config']['cache'];
	if ($cacheLife==0) {
		return false;
	}

	$fname = $INDEX_DIRECTORY."/cache";
	@mkdir($fname);

	$fname .= "/".$theme_name;
	@mkdir($fname);

	$fname .= "/".WT_LOCALE;
	@mkdir($fname);

	$fname .= "/".$GEDCOM;
	@mkdir($fname);

	$fname .= "/".$index."_".$block[0];
	$fp = @fopen($fname, "wb");
	if (!$fp) return false;
	@fwrite($fp, $content);
	@fclose($fp);
	return true;
}

/*
 * Re-worked copy of a similar function in dir_editor.php
 *
 * This function, called recursively, deletes all subdirectories and files in those subdirectories
 *
 * Note:  This function should really be in one of the other "includes/functions/..." scripts.
 */
function removeDir($dir) {
	if (!is_writable($dir)) {
		if (!@chmod($dir, WT_PERM_EXE)) return FALSE;
	}

	$d = dir($dir);
	while (FALSE !== ($entry = $d->read())) {
		if ($entry == '.' || $entry == '..') continue;
		$entry = $dir . '/' . $entry;
		if (is_dir($entry)) {
			if (!removeDir($entry)) return FALSE;
			continue;
		}
		if (!@unlink($entry)) {
			$d->close();
			return FALSE;
		}
	}

	$d->close();
	rmdir($dir);
	return TRUE;
}

/**
 * clears the cache files
 */
function clearCache() {
	global $INDEX_DIRECTORY;

	removeDir("{$INDEX_DIRECTORY}/cache");
}
?>
