<?php
// Template for drawing person boxes
// This template expects that the following variables will be set
//  $pid, $boxID, $icons, $GEDCOM, $style,
// $name, $outBoxAdd, $addname
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2010 PGV Development Team.
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

echo
'<div data-pid="'. $pid . '"' , $outBoxAdd, '>
	<div class="compact_view">',
		$thumbnail,
		'<a href="individual.php?pid=', $pid, '&amp;ged=', rawurlencode($GEDCOM), '" title="',strip_tags($name.$addname),'">
			<span class="namedef name',$style,'">', $shortname, '</span>
		</a>
	</div>
	<div class="inout2 details',$style,'">',
		$person->getLifeSpan(), '
	</div>
	<div class="inout"></div>
</div>';

