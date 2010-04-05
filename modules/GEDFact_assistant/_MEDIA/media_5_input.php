<?php
/**
 * Media Link Assistant Control module for phpGedView
 *
 * Media Link information about an individual
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
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
 * @subpackage Census Assistant
 * @version $Id$
 */

?>
<?php if ($THEME_DIR=="themes/simplygreen/" || $THEME_DIR=="themes/simplyred/" || $THEME_DIR=="themes/simplyblue/") { ?>
	<script>
	var txtcolor="#ffffff";
	</script>
<?php }else{ ?>
	<script>
	var txtcolor="#000000";
	</script>
<?php } ?>

<?php if ($THEME_DIR=="themes/simplygreen/" || $THEME_DIR=="themes/simplyred/" || $THEME_DIR=="themes/simplyblue/") { ?>
	<style type="text/css">
	<!--
	#addlinkQueue td, th { padding: 0.2em; }
	.classy0 { font-family: Verdana, Arial, Helvetica, sans-serif; background-color: transparent; color: #ffffff; font-size: 10px; }
	.classy1 { font-family: Verdana, Arial, Helvetica, sans-serif; background-color: transparent; color: #ffffff; font-size: 10px; }
	-->
	</style>
<?php }else{ ?>
	<style type="text/css">
	<!--
	#addlinkQueue td, th { padding: 0.2em; }
	.classy0 { font-family: Verdana, Arial, Helvetica, sans-serif; background-color: transparent; color: #000000; font-size: 10px; }
	.classy1 { font-family: Verdana, Arial, Helvetica, sans-serif; background-color: transparent; color: #000000; font-size: 10px; }
	-->
	</style>
<?php } 

// Various JavaScript variables required --------------------------------- ?>
<script language="javascript" type="text/javascript">
	var ifamily			= "<?php echo i18n::translate('Open Family Navigator');		?>";
	var remove			= "<?php echo i18n::translate('Remove');			?>";
	var linkExists		= "<?php echo i18n::translate('This link already exists');		?>";
	var imageDir		= "<?php echo $WT_IMAGE_DIR;				?>";
</script>

<?php
echo '<script src="modules/GEDFact_assistant/_MEDIA/media_5_input.js" type="text/javascript"></script>';
?>

	<table width="430" border="0" cellspacing="1" id="addlinkQueue">
		<thead>
		<tr>
			<th class="topbottombar" width="10"  style="font-weight:100;" align="left">#</th>
			<th class="topbottombar" width="55"  style="font-weight:100;" align="left">ID:</th>
			<th class="topbottombar" width="370" style="font-weight:100;" align="left"><?php echo i18n::translate('Name');?></th>
			<th class="topbottombar" width="20"  style="font-weight:100;" align="left"><?php echo i18n::translate('Remove');?></th>
			<th class="topbottombar" width="20"  style="font-weight:100;" align="left"><?php echo i18n::translate('Navigator');?></th>
		</tr>
		</thead>
		<tbody></tbody>
		<tr><td></td></tr>
	</table>
	

