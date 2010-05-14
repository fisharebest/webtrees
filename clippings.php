<?php
/**
 * Family Tree Clippings Cart
 *
 * Uses the $_SESSION["cart"] to store the ids of clippings to download
 * @TODO print a message if people are not included due to privacy
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
 * @subpackage Charts
 * @version $Id$
 */

define('WT_SCRIPT_NAME', 'clippings.php');
require './includes/session.php';
require WT_ROOT.'includes/controllers/clippings_ctrl.php';

$controller = new ClippingsController();
$controller->init();

// -- print html header information
print_header(i18n::translate('Clippings cart'));

if ($ENABLE_AUTOCOMPLETE) require WT_ROOT.'js/autocomplete.js.htm';

echo WT_JS_START;
echo 'function radAncestors(elementid) {var radFamilies=document.getElementById(elementid);radFamilies.checked=true;}';
echo WT_JS_END;

if (count($cart)==0) {?>
<h2><?php print i18n::translate('Family Tree Clippings Cart');?></h2>
<?php }

if ($controller->action=='add') {
	$person = GedcomRecord::getInstance($controller->id);
	print "<b>".$person->getFullName()."</b>";
	if ($controller->type=='fam') {?>
		<form action="clippings.php" method="get">
		<table>
			<tr><td class="topbottombar"><?php print i18n::translate('Which other links from this family would you like to add?')?>
			<input type="hidden" name="id" value="<?php print $controller->id; ?>" />
			<input type="hidden" name="type" value="<?php print $controller->type ?>" />
			<input type="hidden" name="action" value="add1" /></td></tr>
			<tr><td class="optionbox"><input type="radio" name="others" checked value="none" /><?php print i18n::translate('Add just this family record.')?></td></tr>
			<tr><td class="optionbox"><input type="radio" name="others" value="parents" /><?php print i18n::translate('Add parents\' records together with this family record.')?></td></tr>
			<tr><td class="optionbox"><input type="radio" name="others" value="members" /><?php print i18n::translate('Add parents\' and children\'s records together with this family record.')?></td></tr>
			<tr><td class="optionbox"><input type="radio" name="others" value="descendants" /><?php print i18n::translate('Add parents\' and all descendants\' records together with this family record.')?></td></tr>
			<tr><td class="topbottombar"><input type="submit" value="<?php print i18n::translate('Continue Adding')?>" /></td></tr>

		</table>
		</form>
	<?php }
	else if ($controller->type=='indi') {?>
		<form action="clippings.php" method="get">
		<table>
			<tr><td class="topbottombar"><?php print i18n::translate('Which links from this person would you also like to add?')?>
			<input type="hidden" name="id" value="<?php print $controller->id; ?>" />
			<input type="hidden" name="type" value="<?php print $controller->type ?>" />
			<input type="hidden" name="action" value="add1" /></td></tr>
			<tr><td class="optionbox"><input type="radio" name="others" checked value="none" /><?php print i18n::translate('Add just this person.')?></td></tr>
			<tr><td class="optionbox"><input type="radio" name="others" value="parents" /><?php print i18n::translate('Add this person, his parents, and siblings.')?></td></tr>
			<tr><td class="optionbox"><input type="radio" name="others" value="ancestors" id="ancestors" /><?php print i18n::translate('Add this person and his direct line ancestors.')?><br />
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php print i18n::translate('Number of generations:') ?> <input type="text" size="5" name="level1" value="<?php print $MAX_PEDIGREE_GENERATIONS; ?>" onfocus="radAncestors('ancestors');"/></td></tr>
			<tr><td class="optionbox"><input type="radio" name="others" value="ancestorsfamilies" id="ancestorsfamilies" /><?php print i18n::translate('Add this person, his direct line ancestors, and their families.')?><br >
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php print i18n::translate('Number of generations:') ?> <input type="text" size="5" name="level2" value="<?php print $MAX_PEDIGREE_GENERATIONS; ?>" onfocus="radAncestors('ancestorsfamilies');" /></td></tr>
			<tr><td class="optionbox"><input type="radio" name="others" value="members" /><?php print i18n::translate('Add this person, his spouse, and children.')?></td></tr>
			<tr><td class="optionbox"><input type="radio" name="others" value="descendants" id="descendants" /><?php print i18n::translate('Add this person, his spouse, and all descendants.')?><br >
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php print i18n::translate('Number of generations:') ?> <input type="text" size="5" name="level3" value="<?php print $MAX_PEDIGREE_GENERATIONS; ?>" onfocus="radAncestors('descendants');" /></td></tr>
			<tr><td class="topbottombar"><input type="submit" value="<?php print i18n::translate('Continue Adding')?>" />
		</table>
		</form>
	<?php } else if ($controller->type=='sour')  {?>
		<form action="clippings.php" method="get">
		<table>
			<tr><td class="topbottombar"><?php print i18n::translate('Which records linked to this source should be added?')?>
			<input type="hidden" name="id" value="<?php print $controller->id; ?>" />
			<input type="hidden" name="type" value="<?php print $controller->type ?>" />
			<input type="hidden" name="action" value="add1" /></td></tr>
			<tr><td class="optionbox"><input type="radio" name="others" checked value="none" /><?php print i18n::translate('Add just this source.')?></td></tr>
			<tr><td class="optionbox"><input type="radio" name="others" value="linked" /><?php print i18n::translate('Add this source and families/people linked to it.')?></td></tr>
			<tr><td class="topbottombar"><input type="submit" value="<?php print i18n::translate('Continue Adding')?>" />
		</table>
		</form>
	<?php }
	}
$ct = count($cart);

if ($controller->privCount>0) {
	print "<span class=\"error\">".i18n::translate('Some items could not be added due to privacy restrictions')."</span><br /><br />\n";
}

if ($ct==0) {

	// -- new lines, added by Jans, to display helptext when cart is empty
	if ($controller->action!='add') {
		echo i18n::translate('The Clippings Cart allows you to take extracts ("clippings") from this family tree and bundle them up into a single file for downloading and subsequent importing into your own genealogy program.  The downloadable file is recorded in GEDCOM format.<br /><ul><li>How to take clippings?<br />This is really simple. Whenever you see a clickable name (individual, family, or source) you can go to the Details page of that name. There you will see the <b>Add to Clippings Cart</b> option.  When you click that link you will be offered several options to download.</li><li>How to download?<br />Once you have items in your cart, you can download them just by clicking the <b>Download Now</b> link.  Follow the instructions and links.</li></ul>');

		echo WT_JS_START;
		echo 'var pastefield;';
		echo 'function paste_id(value) {pastefield.value=value;}';
		echo WT_JS_END;
		?>
		<form method="get" name="addin" action="clippings.php">
		<table>
		<tr>
			<td colspan="2" class="topbottombar" style="text-align:center; ">
				<?php echo i18n::translate('Add Individual By ID'), help_link('add_by_id'); ?>
			</td>
		</tr>
		<tr>
			<td class="optionbox">
				<input type="hidden" name="action" value="add"/>
				<input type="text" name="id" id="cart_item_id" size="5"/>
			</td>
			<td class="optionbox">
				<?php print_findindi_link('cart_item_id', ''); ?>
				<?php print_findfamily_link('cart_item_id', ''); ?>
				<?php print_findsource_link('cart_item_id', ''); ?>
				<input type="submit" value="<?php print i18n::translate('Add');?>"/>

			</td>
		</tr>
		</table>
		</form>
		<?php
	}

	// -- end new lines
	print "\r\n\t\t<br /><br />".i18n::translate('Your Clippings Cart is empty.')."<br /><br />";
} else {
	if ($controller->action != 'download' && $controller->action != 'add') { ?>
		<form method="get" action="clippings.php">
		<input type="hidden" name="action" value="download" />
		<table><tr><td class="width33" valign="top" rowspan="3">
		<table>
		<tr><td colspan="2" class="topbottombar"><h2><?php print i18n::translate('File Information') ?></h2></td></tr>
		<tr>
		<td class="descriptionbox width50 wrap"><?php echo i18n::translate('File Type'), help_link('file_type'); ?></td>
		<td class="optionbox">
		<?php if ($TEXT_DIRECTION=='ltr') { ?>
			<input type="radio" name="filetype" checked="checked" value="gedcom" />&nbsp;GEDCOM<br/><input type="radio" name="filetype" value="gramps" DISABLED />&nbsp;Gramps XML <!-- GRAMPS doesn't work right now -->
		<?php } else { ?>
			GEDCOM&nbsp;<?php print getLRM();?><input type="radio" name="filetype" checked="checked" value="gedcom" /><?php print getLRM();?><br />Gramps XML&nbsp;<?php print getLRM();?><input type="radio" name="filetype" value="gramps" /><?php print getLRM();?>
		<?php } ?>
		</td></tr>

		<tr><td class="descriptionbox width50 wrap"><?php echo i18n::translate('Zip File(s)'), help_link('zip'); ?></td>
		<td class="optionbox"><input type="checkbox" name="Zip" value="yes" checked="checked" /></td></tr>

		<tr><td class="descriptionbox width50 wrap"><?php echo i18n::translate('Include media (automatically zips files)'), help_link('include_media'); ?></td>
		<td class="optionbox"><input type="checkbox" name="IncludeMedia" value="yes" checked="checked" /></td></tr>

		<?php
		// Determine the Privatize options available to this user
		if (WT_USER_IS_ADMIN) {
			$radioPrivatizeNone = 'checked="checked" ';
			$radioPrivatizeVisitor = '';
			$radioPrivatizeUser = '';
			$radioPrivatizeGedadmin = '';
			$radioPrivatizeAdmin = '';
		} else if (WT_USER_GEDCOM_ADMIN) {
			$radioPrivatizeNone = 'DISABLED ';
			$radioPrivatizeVisitor = 'checked="checked" ';
			$radioPrivatizeUser = '';
			$radioPrivatizeGedadmin = '';
			$radioPrivatizeAdmin = 'DISABLED ';
		} else if (WT_USER_ID) {
			$radioPrivatizeNone = 'DISABLED ';
			$radioPrivatizeVisitor = 'checked="checked" ';
			$radioPrivatizeUser = '';
			$radioPrivatizeGedadmin = 'DISABLED ';
			$radioPrivatizeAdmin = 'DISABLED ';
		} else {
			$radioPrivatizeNone = 'DISABLED ';
			$radioPrivatizeVisitor = 'checked="checked" DISABLED ';
			$radioPrivatizeUser = 'DISABLED ';
			$radioPrivatizeGedadmin = 'DISABLED ';
			$radioPrivatizeAdmin = 'DISABLED ';
		}
		?>

		<tr><td class="descriptionbox width50 wrap"><?php echo i18n::translate('Apply privacy settings?'), help_link('apply_privacy'); ?></td>
		<td class="list_value">
		<input type="radio" name="privatize_export" value="none" <?php print $radioPrivatizeNone; ?>/>&nbsp;<?php print i18n::translate('None'); ?><br />
		<input type="radio" name="privatize_export" value="visitor" <?php print $radioPrivatizeVisitor; ?>/>&nbsp;<?php print i18n::translate('Visitor'); ?><br />
		<input type="radio" name="privatize_export" value="user" <?php print $radioPrivatizeUser; ?>/>&nbsp;<?php print i18n::translate('Authenticated user'); ?><br />
		<input type="radio" name="privatize_export" value="gedadmin" <?php print $radioPrivatizeGedadmin; ?>/>&nbsp;<?php print i18n::translate('GEDCOM administrator'); ?><br />
		<input type="radio" name="privatize_export" value="admin" <?php print $radioPrivatizeAdmin; ?>/>&nbsp;<?php print i18n::translate('Site administrator'); ?>
		</td></tr>

		<tr><td class="descriptionbox width50 wrap"><?php echo i18n::translate('Convert from UTF-8 to ANSI (ISO-8859-1)'), help_link('utf8_ansi'); ?></td>
		<td class="optionbox"><input type="checkbox" name="convert" value="yes" /></td></tr>

		<tr><td class="descriptionbox width50 wrap"><?php echo i18n::translate('Remove custom webtrees tags? (eg. _WT_USER, _THUM)'), help_link('remove_tags'); ?></td>
		<td class="optionbox"><input type="checkbox" name="remove" value="yes" checked="checked" /></td></tr>

		<tr><td class="descriptionbox width50 wrap"><?php echo i18n::translate('Convert media path to'), help_link('convertPath'); ?></td>
		<td class="list_value"><input type="text" name="conv_path" size="30" value="<?php echo getLRM(), $controller->conv_path, getLRM();?>" /></td></tr>

		<tr><td class="descriptionbox width50 wrap"><?php echo i18n::translate('Convert media folder separators to'), help_link('convertSlashes'); ?></td>
		<td class="list_value">
		<input type="radio" name="conv_slashes" value="forward" <?php if ($controller->conv_slashes=='forward') print "checked=\"checked\" "; ?>/>&nbsp;<?php print i18n::translate('Forward slashes : /');?><br />
		<input type="radio" name="conv_slashes" value="backward" <?php if ($controller->conv_slashes=='backward') print "checked=\"checked\" "; ?>/>&nbsp;<?php print i18n::translate('Backslashes : \\');?>
		</td></tr>

		<tr><td class="topbottombar" colspan="2">
		<input type="submit" value="<?php echo i18n::translate('Download Now'); ?>" />
		</td></tr>
		</form>

		</td></tr>
		</table>
		<br />

		<script language="JavaScript" type="text/javascript">
		<!--
		var pastefield;
		function paste_id(value)
		{
			pastefield.value=value;
		}
		//-->
		</script>
		<form method="get" name="addin" action="clippings.php">
		<table>
		<tr>
			<td colspan="2" class="topbottombar" style="text-align:center; ">
				<?php echo i18n::translate('Add Individual By ID'), help_link('add_by_id'); ?>
			</td>
		</tr>
		<tr>
			<td class="optionbox">
				<input type="hidden" name="action" value="add"/>
				<input type="text" name="id" id="cart_item_id" size="8" />
			</td>
			<td class="optionbox">
				<?php print_findindi_link('cart_item_id', ''); ?>
				<?php print_findfamily_link('cart_item_id', ''); ?>
				<?php print_findsource_link('cart_item_id', ''); ?>
				<input type="submit" value="<?php print i18n::translate('Add');?>"/>

			</td>
		</tr>
		</table>
		</form>


	<?php } ?>
	<br /><a href="clippings.php?action=empty"><?php echo i18n::translate('Empty Cart'), help_link('empty_cart', 'clippings'); ?></a>
	</td></tr>

	<tr><td class="topbottombar"><h2><?php echo i18n::translate('Family Tree Clippings Cart'), help_link('clip_cart', 'clippings'); ?></h2></td></tr>

	<tr><td valign="top">
	<table id="mycart" class="sortable list_table width100">
		<tr>
			<th class="list_label"><?php echo i18n::translate('Type')?></th>
			<th class="list_label"><?php echo i18n::translate('ID')?></th>
			<th class="list_label"><?php echo i18n::translate('Name / Description')?></th>
			<th class="list_label"><?php echo i18n::translate('Remove')?></th>
		</tr>
<?php
	for ($i=0; $i<$ct; $i++) {
		$clipping = $cart[$i];
		$tag = strtoupper(substr($clipping['type'], 0, 4)); // source => SOUR
		//print_r($clipping);
		//-- don't show clippings from other gedcoms
		if ($clipping['gedcom']==$GEDCOM) {
			if ($tag=='INDI') $icon = "indis";
			if ($tag=='FAM' ) $icon = "sfamily";
			if ($tag=='SOUR') $icon = "source";
			if ($tag=='REPO') $icon = "repository";
			if ($tag=='NOTE') $icon = "notes";
			if ($tag=='OBJE') $icon = "media";
			?>
			<tr><td class="list_value">
				<?php if (!empty($icon)) { ?><img src="<?php echo $WT_IMAGE_DIR, "/", $WT_IMAGES[$icon]["small"];?>" border="0" alt="<?php echo $tag;?>" title="<?php echo $tag;?>" /><?php } ?>
			</td>
			<td class="list_value ltr"><?php echo $clipping['id']?></td>
			<td class="list_value">
			<?php
			$record=GedcomRecord::getInstance($clipping['id']);
			if ($record) echo '<a href="', encode_url($record->getLinkUrl()), '">', PrintReady($record->getListName()), '</a>';
			?>
			</td>
			<td class="list_value center vmiddle"><a href="clippings.php?action=remove&amp;item=<?php echo $i;?>"><img src="<?php echo $WT_IMAGE_DIR, "/", $WT_IMAGES["remove"]["other"];?>" border="0" alt="<?php echo i18n::translate('Remove')?>" title="<?php echo i18n::translate('Remove');?>" /></a></td>
		</tr>
		<?php
		}
	}
?>
	</table>
	</td></tr></table>
<?php
}
if (isset($_SESSION["cart"])) $_SESSION["cart"]=$cart;
print_footer();
?>
