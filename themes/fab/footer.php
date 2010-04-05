<?php
// Footer for FAB theme
//
// webtrees: Web based Family History software
// Copyright (C) 2010 webtrees development team.
//
// Derived from PhpGedView
// Modifications Copyright (c) 2010 Greg Roach
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
// @package webtrees
// @subpackage Themes
// @version $Id$

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

echo '</div><div id="footer" class="block">', contact_links();

if ($SHOW_STATS || WT_DEBUG) {
	echo execution_stats();
}
echo '</div>';
echo '<div id="footer" ><a href="', WT_WEBTREES_URL, '" target="_blank" alt="', WT_WEBTREES, WT_USER_IS_ADMIN? (" - " .WT_VERSION_TEXT): "" , '" title="', WT_WEBTREES , WT_USER_IS_ADMIN? (" - " .WT_VERSION_TEXT): "", '"><span style="font-size:150%; color:#888888;">', WT_WEBTREES, '</span></a></div>';
