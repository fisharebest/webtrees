<?php
// Template for drawing person boxes
// This template expects that the following variables will be set
//  $pid, $boxID, $icons, $GEDCOM, $style,
// $name, $classfacts, $genderImage, $BirthDeath, $isF, $outBoxAdd,
// $addname, $showid, $float
//
// webtrees: Web based Family History software
// Copyright (C) 2013 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2010 PGV Development Team.  All rights reserved.
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

echo '<div id="out-', $boxID ,'" ', $outBoxAdd, '>
	<div class="compact_view">',
		$thumbnail,
		'<a onclick="event.cancelBubble=true;" href="individual.php?pid=', $pid, '&amp;ged=', rawurlencode($GEDCOM), '" title="',strip_tags($name.$addname),'">
			<span id="namedef-',$boxID, '" class="name',$style,' ',$classfacts,'">', $shortname, '</span>
		</a>
		<p>', $person->getLifeSpan(), '</p>
		<p>', $birthplace, '</p>
	</div>';
	//	details for zoom view
		echo '<div id="fontdef-',$boxID,'" class="details',$style,'" style="display:none;">
			<br><hr>
				<a onclick="event.cancelBubble=true;" href="individual.php?pid=', $pid, '&amp;ged=', rawurlencode($GEDCOM), '">',
					'<span id="namedef-',$boxID, '.2" class="name',$style,' ',$classfacts,'">', $name.$addname, '</span>
					<span class="name',$style,'">',$genderImage,'</span>
				</a>',
			$BirthDeath,
		'</div>
		<div id="inout-',$boxID,'" style="display:none;">
			<div id="LOADING-inout-',$boxID,'">',WT_I18N::translate('Loading…'),'</div>
		</div>';
	// end of zoom view
echo '</div>';
?>
