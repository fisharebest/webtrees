<?php
// Footer for FAB theme
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
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
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

echo '</div>'; // <div id="content">
if ($view!='simple') {
	echo '<div id="footer"><div class="block">', contact_links();
	if ($WT_TREE && $WT_TREE->getPreference('SHOW_STATS')) {
		echo execution_stats();
	}
	echo '</div><a style="font-size:150%; color:#888;" href="', WT_WEBTREES_URL, '" title="', WT_WEBTREES , ' - ', WT_VERSION, '">', WT_WEBTREES, '</a></div>';
}
