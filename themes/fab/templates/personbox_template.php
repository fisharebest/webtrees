<?php
// Template for drawing person boxes
//
// This template expects that the following variables will be set
// $pid, $boxID, $icons, $GEDCOM, $style,
// $name, $classfacts, $genderImage, $BirthDeath, $isF, $outBoxAdd,
// $addname, $showid, $float
//
// Copyright (c) 2010 Greg Roach
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
// $Id$

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}
echo
	'<div id="out-', $boxID, '" ', $outBoxAdd, '>';
	if ($show_full) { 
		echo '<div class="noprint" id="icons-',$boxID,'"';
		echo 'style="',$iconsStyleAdd,' width: 25px; height: 50px">';
		echo $icons;
		echo '</div>';
	}	
echo $thumbnail; ?>
<a onclick="event.cancelBubble = true;" href="individual.php?pid=<?php echo $pid; ?>&amp;ged=<?php echo rawurlencode($GEDCOM); ?>">

<?php
echo '<span id="namedef-',$boxID, '" class="name',$style,' ',$classfacts;'">';

if ($show_full) { 
	echo $name.$addname;
	} else {
	echo $name; // do not print additional names
}

echo ' </span>';
if 	(!$show_full) { 
	echo '<div class="person_box_lifespan" >',
		 $person->getLifeSpan();
	echo '</div>';
}

echo '<span class="name',$style,'" ',$genderImage,'</span>';
echo $showid; 
echo '</a>',
	 '<div id="fontdef-',$boxID,'" class="details',$style,'">';
echo '<div id="inout2-', $boxID,'" style="display: block; max-height:', ($bheight*.9),'px;">',$BirthDeath,'</div>';

echo '</div>',
	 '<div id="inout-',$boxID,'" style="display: none;">',
	 '<div id="LOADING-inout-',$boxID,'">',WT_I18N::translate('Loading...'),'</div>',
	 '</div>',
	 '</div>';
