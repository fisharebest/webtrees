<?php
/**
 * Media Link Assistant Control module for webtrees
 *
 * Media Link information about an individual
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2011 webtrees development team.
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
 * @subpackage GEDFact_assistant
 * @version $Id$
*/

if (stristr($_SERVER["SCRIPT_NAME"], basename(__FILE__))!==false) {
	echo "You cannot access an include file directly.";
	exit;
}
?>

<html <?php echo WT_I18N::html_markup(); ?>>
<head>
</head>
<body>
<?php

	$links = get_media_relations($mediaid);
	echo "<table><tr><td>";
	echo "<table id=\"existLinkTbl\" width=\"430\" cellspacing=\"1\" >";
	echo "<tr>";
	echo '<td class="topbottombar" width="15"  style="font-weight:100;" >#</td>';
	echo '<td class="topbottombar" width="50"  style="font-weight:100;" >ID:</td>';
	echo '<td class="topbottombar" width="340" style="font-weight:100;" >', WT_I18N::translate('Name'), '</td>';
	echo '<td class="topbottombar" width="20"  style="font-weight:100;" >', WT_I18N::translate('Keep'), '</td>';
	echo '<td class="topbottombar" width="20"  style="font-weight:100;" >', WT_I18N::translate('Remove'), '</td>';
	echo '<td class="topbottombar" width="20"  style="font-weight:100;" >', WT_I18N::translate('Navigator'), '</td>';
	echo "</tr>";

	$i=1;
	foreach (array_keys($links) as $link) {
		$record=WT_GedcomRecord::getInstance($link);
		echo "<tr ><td>";
		echo $i++;
		echo "</td><td id=\"existId_", $i, "\" class=\"row2\">";
		echo $link;
		echo "</td><td>";
		echo $record->getFullName();
		echo "</td>";
		echo "<td align='center'><input alt='", WT_I18N::translate('Keep Link in list'), "', title='", WT_I18N::translate('Keep Link in list'), "' type='radio' id='", $link, "_off' name='", $link, "' checked /></td>";
		echo "<td align='center'><input alt='", WT_I18N::translate('Remove Link from list'), "', title='", WT_I18N::translate('Remove Link from list'), "' type='radio' id='", $link, "_on'  name='", $link, "' /></td>";

		if ($record->getType()=='INDI') {
			?>
			<td align="center"><a href="#"><img style="border-style:none; margin-top:5px;" src="<?php echo $WT_IMAGES['button_family']; ?>" alt="<?php echo WT_I18N::translate('Open Family Navigator'); ?>" title="<?php echo WT_I18N::translate('Open Family Navigator'); ?>" name="family_'<?php echo $link; ?>'" onclick="javascript:openFamNav('<?php echo $link; ?>');" /></a></td>
			<?php
		} elseif ($record->getType()=='FAM') {
			if ($record->getHusband()) {
				$head=$record->getHusband()->getXref();
			} elseif ($record->getWife()) {
				$head=$record->getWife()->getXref();
			} else {
				$head='';
			}
			?>
			<td align="center"><a href="#"><img style="border-style:none; margin-top:5px;" src="<?php echo $WT_IMAGES['button_family']; ?>" alt="<?php echo WT_I18N::translate('Open Family Navigator'); ?>" title="<?php echo WT_I18N::translate('Open Family Navigator'); ?>" name="family_'<?php echo $link; ?>'" onclick="javascript:openFamNav('<?php echo $head; ?>');" /></a></td>
			<?php
		} else {
			echo '<td></td>';
		}
		echo '</tr>';
	}

	echo "</table>";
	echo "</td></tr></table>";
	echo "<br />";
?>

</body>
</html>
