<?php
// Custom pdfviewer for the JustBlack theme
//
// webtrees: Web based Family History software
// Copyright (C) 2013 JustCarmen.
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
// $Id: pdfviewer.php 2013-09-15 JustCarmen $

// Note: this script is not checking for private media because google docs viewer can't reach them. But if the media is private the thumbnails won't be listed,
// so this script can be used safely. The script is checking if a file exists. If not the built-in gdocs viewer exit message will be shown.

define('WT_SCRIPT_NAME', 'pdfviewer.php');
require './../../../includes/session.php';

Zend_Session::writeClose();

$mid   = WT_Filter::get('mid', WT_REGEX_XREF);
$media = WT_Media::getInstance($mid);

function send404AndExit() {
	header('HTTP/1.0 404 Not Found');
	header('Status: 404 Not Found');
	exit;
}

// Media file somewhere else?
if ($media->isExternal()) {
	header('Location: ' . $media->getFilename());
	exit;
}

$serverFilename = WT_ROOT.'data'.$media->getServerFileName();

if (!file_exists($serverFilename)) {
	send404AndExit();
}

$protocol = $_SERVER["SERVER_PROTOCOL"];  // determine if we are using HTTP/1.0 or HTTP/1.1
$filetime = $media->getFiletime('main');
$filetimeHeader = gmdate("D, d M Y H:i:s", $filetime).' GMT';
$expireOffset = 3600 * 24;  // tell browser to cache this image for 24 hours
if (safe_GET('cb')) $expireOffset = $expireOffset * 7; // if cb parameter was sent, cache for 7 days 
$expireHeader = gmdate("D, d M Y H:i:s", WT_TIMESTAMP + $expireOffset) . " GMT";

$etag = $media->getEtag('main');

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

// add caching headers.  allow browser to cache file, but not proxy
header("Last-Modified: " . $filetimeHeader);
header('ETag: "'.$etag.'"');
header("Expires: ".$expireHeader);
header("Cache-Control: max-age=".$expireOffset.", s-maxage=0, proxy-revalidate");

// if this file is already in the user’s cache, don’t resend it
// first check if the if_modified_since param matches
if (($if_modified_since == $filetimeHeader)) {
	// then check if the etag matches
	if ($if_none_match == $etag) {
		header($protocol." 304 Not Modified");
		exit;
	}
}

// send headers for the pdf-file
header('Content-Type: ' . $media->mimeType());
header('Content-Disposition: filename="' . addslashes(basename($media->file)) . '"');

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

?>