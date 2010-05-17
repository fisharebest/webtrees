<?php
/**
 * Template for drawing person boxes
 * This template expects that the following variables will be set
 *  $pid, $boxID, $personlinks, $icons, $title, $GEDCOM, $style,
 * $name, $classfacts, $genderImage, $BirthDeath, $isF, $outBoxAdd,
 * $addname, $showid, $float
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @package webtrees
 * @subpackage Themes
 * @version $Id: personbox_template.php 4194 2008-10-30 10:45:46Z fisharebest $
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

?>
<div id="I<?php print $boxID; ?>links"
	style="position:absolute; left:0px; top:0px; width:<?php print $lbwidth; ?>px; visibility:hidden; z-index:100;">
<?php print $personlinks; ?></div>
<div id="out-<?php print $boxID; ?>" <?php print $outBoxAdd; ?>>
<!--  table helps to maintain spacing -->
<table width="100%" cellspacing="0" cellpadding="0" border="0"><tr><td valign="top">
		<div id="icons-<?php print $boxID; ?>"
			style="<?php print $iconsStyleAdd; ?> width: 25px; height: 50px;"><?php print $icons; ?>
		</div>
		<?php print $thumbnail; ?>
		<a onclick="event.cancelBubble = true;"
			href="individual.php?pid=<?php print $pid; ?>&amp;ged=<?php print $GEDCOM; ?>"
			title="<?php print $title; ?>">
		<span id="namedef-<?php print $boxID; ?>" class="name<?php print $style; ?> <?php print $classfacts; ?>">
			<?php print $name.$addname; ?>
		</span>
		<span class="name<?php print $style; ?>"> <?php print $genderImage; ?></span>
		<?php print $showid; ?> </a>
		<div id="fontdef-<?php print $boxID; ?>" class="details<?php print $style; ?>">
			<div id="inout2-<?php print $boxID; ?>" style="display: block;"><?php print $BirthDeath; ?></div>
		</div>
		<div id="inout-<?php print $boxID; ?>" style="display: none;">
			<div id="LOADING-inout-<?php print $boxID; ?>"><?php print i18n::translate('Loading...'); ?></div>
		</div>
</td></tr></table>
</div>
