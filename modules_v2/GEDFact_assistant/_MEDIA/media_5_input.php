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
 * Copyright (C) 2002 to 2008  PGV Development Team.  All rights reserved.
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

?>

	<style type="text/css">
	<!--
	.classy0 { font-family: Verdana, Arial, Helvetica, sans-serif; background-color: transparent; color: #000000; font-size: 10px; }
	.classy1 { font-family: Verdana, Arial, Helvetica, sans-serif; background-color: transparent; color: #000000; font-size: 10px; }
	-->
	</style>
	
<?php

global $WT_IMAGES;

// Various JavaScript variables required --------------------------------- ?>
<script language="javascript" type="text/javascript">
	var ifamily = "<?php echo WT_I18N::translate('Open Family Navigator'); ?>";
	var remove = "<?php echo WT_I18N::translate('Remove'); ?>";
	var linkExists = "<?php echo WT_I18N::translate('This link already exists'); ?>";
	/* ===icons === */
	var removeLinkIcon = "<?php echo $WT_IMAGES['remove']; ?>";
	var familyNavIcon = "<?php echo $WT_IMAGES['button_family']; ?>";
</script>

<?php
echo '<script src="', WT_MODULES_DIR, 'GEDFact_assistant/_MEDIA/media_5_input.js" type="text/javascript"></script>';
?>

	<table width="430" border="0" cellspacing="1" id="addlinkQueue">
		<thead>
		<tr>
			<th class="topbottombar" width="10"  style="font-weight:100;" align="left">#</th>
			<th class="topbottombar" width="55"  style="font-weight:100;" align="left">ID:</th>
			<th class="topbottombar" width="370" style="font-weight:100;" align="left"><?php echo WT_I18N::translate('Name'); ?></th>
			<th class="topbottombar" width="20"  style="font-weight:100;" align="left"><?php echo WT_I18N::translate('Remove'); ?></th>
			<th class="topbottombar" width="20"  style="font-weight:100;" align="left"><?php echo WT_I18N::translate('Navigator'); ?></th>
		</tr>
		</thead>
		<tbody></tbody>
		<tr><td></td></tr>
	</table>
