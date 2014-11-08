<?php
// Footer for Colors theme
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

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

echo '</div>'; // <div id="content">
if ($view!='simple') {
	echo '<div id="footer" class="', $TEXT_DIRECTION, '">';
	echo '<br>';
	echo contact_links();
	echo '<br>';
	echo '<p class="logo">';
	echo '<a href="', WT_WEBTREES_URL, '" target="_blank" class="icon-webtrees" title="', WT_WEBTREES, ' ', WT_VERSION, '"></a>';
	echo '</p>';
	if ($WT_TREE && $WT_TREE->getPreference('SHOW_STATS')) {
		echo execution_stats();
	}
	if (exists_pending_change()) {
		echo '<a href="#" onclick="window.open(\'edit_changes.php\', \'_blank\', chan_window_specs); return false;">';
		echo '<p class="error center">', WT_I18N::translate('There are pending changes for you to moderate.'), '</p>';
		echo '</a>';
	}
	echo '</div>'; // <div id="footer">
}
