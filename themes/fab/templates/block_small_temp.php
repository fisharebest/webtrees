<?php
// Template for drawing the height-restricted blocks on the portal pages
//
// This template expects that the following variables will be set
// $id - the DOM id for the block div
// $title - the title of the block
// $content - the content of the block
//
// Copyright (C) 2010 Greg Roach.  All rights reserved.

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}
echo '<div id="', $id, '" class="block"><b>', $title, '</b><div style="max-height:240px; overflow:auto;">', $content, '</div></div>';
