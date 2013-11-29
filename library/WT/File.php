<?php
// File manipulation utilities
//
// webtrees: Web based Family History software
// Copyright (c) 2013 webtrees development team
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

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class WT_File {
	//////////////////////////////////////////////////////////////////////////////
	// Fetch a remote file
	// Note that fopen() and file_get_contents() are often unvailable, as they
	// can easily be exploited by application bugs, and are therefore disabled.
	//////////////////////////////////////////////////////////////////////////////

	public static function fetchUrl($url, $timeout=3) {
		$host = parse_url($url, PHP_URL_HOST);
		$port = parse_url($url, PHP_URL_PORT);
		$path = parse_url($url, PHP_URL_PATH);

		if (!$port) {
			$port = parse_url($url, PHP_URL_SCHEME) == 'https' ? 443 : 80;
		}

		$scheme = $port == 443 ? 'ssl://' : '';

		$fp = @fsockopen($scheme . $host, $port, $errno, $errstr, $timeout);
		if (!$fp) {
			return null;
		}

		fputs($fp, "GET $path HTTP/1.0\r\nHost: $host\r\nConnection: Close\r\n\r\n");

		$response = '';
		while ($data = fread($fp, 8192)) {
			$response .= $data;
		}
		fclose($fp);

		// The file has moved?  Follow it.
		if (preg_match('/^HTTP\/1.[01] 30[23].+\nLocation: ([^\r\n]+)/s', $response, $match)) {
			return WT_File::fetchUrl($match[1]);
		} else {
			// The response includes headers, a blank line, then the content
			return substr($response, strpos($response, "\r\n\r\n") + 4);
		}
	}

	//////////////////////////////////////////////////////////////////////////////
	// Recursively delete a folder or file
	//////////////////////////////////////////////////////////////////////////////

	public static function delete($path) {
		// In case the file is marked read-only
		@chmod($path, 0777);

		if (is_dir($path)) {
			$dir = opendir($path);
			while ($dir !== false && (($file = readdir($dir)) !== false)) {
				if ($file != '.' && $file != '..') {
					WT_File::delete($path . DIRECTORY_SEPARATOR . $file);
				}
			}
			closedir($dir);
			@rmdir($path);
		} else {
			@unlink($path);
		}
		return !file_exists($path);
	}

	//////////////////////////////////////////////////////////////////////////////
	// Create a folder, and subfolders, if it does not already exist
	//////////////////////////////////////////////////////////////////////////////

	public static function mkdir($path) {
		if (!is_dir($path)) {
			if (!is_dir(dirname($path))) {
				WT_File::mkdir(dirname($path));
			}
			@mkdir($path);
			return is_dir($path);
		} else {
			return true;
		}
	}
}
