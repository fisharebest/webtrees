<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2017 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
namespace Fisharebest\Webtrees;

use ErrorException;
use Fisharebest\Webtrees\Functions\FunctionsMedia;
use Intervention\Image\Exception\NotReadableException;
use Intervention\Image\Exception\NotSupportedException;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Glide\Filesystem\FileNotFoundException;
use League\Glide\ServerFactory;
use League\Glide\Signatures\SignatureFactory;
use League\Glide\Signatures\SignatureException;

/** @global Tree $WT_TREE */
global $WT_TREE;

require 'includes/session.php';

$mid   = Filter::get('mid', WT_REGEX_XREF);
$media = Media::getInstance($mid, $WT_TREE);

$media_file = '';
$media_dir  = '';

if ($media !== null) {
	// media file somewhere else?
	if ($media->isExternal()) {
		header('Location: ' . $media->getFilename());

		return;
	} elseif (!$media->canShow()) {
		FunctionsMedia::outputHttpStatusAsImage(403, 'Not allowed');

		return;
	}

	$media_dir  = $media->getTree()->getPreference('MEDIA_DIRECTORY');
	$media_file = $media->getFilename();
} elseif (Auth::isAdmin()) {
	// admin_media.php needs to create thumbnails for files that have no media object.
	$media_file = Filter::get('unused');
}

try {
	// Prefer imagemagick, as it supports a wider range of image formats.
	if (extension_loaded('imagick')) {
		$driver = 'imagick';
	} else {
		$driver = 'gd';
	}

	// Setup Glide server
	// Caution - $media_dir may contain relative paths: ../../
	$source_dir = new Filesystem(new Local(WT_DATA_DIR . $media_dir));
	$cache_dir  = new Filesystem(new Local(WT_DATA_DIR . 'thumbnail-cache/' . md5($media_dir)));
	$assets_dir = new Filesystem(new Local( 'assets'));

	$server = ServerFactory::create([
		'driver'     => $driver,
		'source'     => $source_dir,
		'cache'      => $cache_dir,
		'watermarks' => $assets_dir,
	]);

	// Validate HTTP signature
	$glide_key = Site::getPreference('glide-key');
	SignatureFactory::create($glide_key)->validateRequest(parse_url(WT_BASE_URL . 'mediafirewall.php', PHP_URL_PATH), $_GET);

	// Generate and send the image
	$error_reporting = error_reporting(0);
	$server->outputImage($media_file, $_GET);
	error_reporting($error_reporting);
} catch (SignatureException $ex) {
	DebugBar::addThrowable($ex);

	FunctionsMedia::outputHttpStatusAsImage(403, 'Not allowed');
} catch (FileNotFoundException $ex) {
	DebugBar::addThrowable($ex);

	FunctionsMedia::outputHttpStatusAsImage(404, 'Not found');
} catch (NotReadableException $ex) {
	DebugBar::addThrowable($ex);

	FunctionsMedia::outputHttpStatusAsImage(500, 'Error');
	Log::addMediaLog("Unable to read image from " . $media_file . "\n" . $ex->getMessage());
} catch (NotSupportedException $ex) {
	DebugBar::addThrowable($ex);

	FunctionsMedia::outputHttpStatusAsImage(500, 'Error');
	Log::addMediaLog("Install php-gd or php-imagick to create thumbnails.\n" . $ex->getMessage());
} catch (ErrorException $ex) {
	DebugBar::addThrowable($ex);

	FunctionsMedia::outputHttpStatusAsImage(500, 'Error');
	Log::addMediaLog("Unsable to create thumbnail from " . $media_file . "\n" . $ex->getMessage());
}
