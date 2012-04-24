<?php
// Lightbox Album module for webtrees
//
// Display media Items using Lightbox
//
// webtrees: Web based Family History software
// Copyright (C) 2011 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2007 to 2009  PGV Development Team.  All rights reserved.
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

global $edit, $sort_i;

$reorder=safe_get('reorder', '1', '0');

require_once WT_ROOT.WT_MODULES_DIR.'lightbox/functions/lightbox_print_media.php';

function cut_html($string) {
    $a=$string;

    while ($a = strstr($a, '&')) {
        $b=strstr($a, ';');
        if (!$b) {
            $nb=strlen($a);
            return substr($string, 0, strlen($string)-$nb);
        }
        $a=substr($a, 1, strlen($a)-1);
    }
    return $string;
}

if (!isset($edit)) {
	$edit=1;
}

// Used when sorting media on album tab page ===============================================
if ($reorder==1) {
	$sort_i=0; // Used in sorting on lightbox_print_media.php page
	?>
	<script type="text/javascript">
	<!--
	// This script saves the dranNdrop reordered info into a hidden form input element (name=order2)
	function saveOrder() {
		// var sections = document.getElementsByClassName('section');
		var sections = $$('.section');
		var order = '';
		sections.each(function(section) {
			order += Sortable.sequence(section) + ',';
			document.getElementById('ord2').value = order;
		});
	};
	//-->
	</script>
	<form name="reorder_form" method="post" action="edit_interface.php">
		<input type="hidden" name="action" value="al_reorder_media_update">
		<input type="hidden" name="pid" value="<?php echo $controller->record->getXref(); ?>">
		<input type="hidden" id="ord2" name="order2" value="">
		<center>
			<button type="submit" title="<?php echo WT_I18N::translate('Saves the sorted media to the database'); ?>" onclick="saveOrder();" ><?php echo WT_I18N::translate('Save'); ?></button>&nbsp;
			<button type="submit" title="<?php echo WT_I18N::translate('Reset to the original order'); ?>" onclick="document.reorder_form.action.value='al_reset_media_update'; document.reorder_form.submit();"><?php echo WT_I18N::translate('Reset'); ?></button>&nbsp;
			<button type="button" title="<?php echo WT_I18N::translate('Quit and return'); ?>" onClick="location.href='<?php echo WT_SCRIPT_NAME, '?pid=', $controller->record->getXref(), '#lightbox'; ?>'"><?php echo WT_I18N::translate('Cancel'); ?></button>
			<?php
			/*
			// Debug ---------------------------------------------------------------------------
			&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="button" onClick="getGroupOrder()" value="Debug: Sorted">
			// ------------------------------------------------------------------------------------
			*/
			?>
		</center>
	</form>
	<?php
}
echo '<table width="100%" cellpadding="0" border="0"><tr>';
echo '<td width="100%" valign="top" >';
lightbox_print_media($controller->record->getXref(), 0, true, 1); // map, painting, photo, tombstone)
lightbox_print_media($controller->record->getXref(), 0, true, 2); // card, certificate, document, magazine, manuscript, newspaper
lightbox_print_media($controller->record->getXref(), 0, true, 3); // electronic, fiche, film
lightbox_print_media($controller->record->getXref(), 0, true, 4); // audio, book, coat, video, other
lightbox_print_media($controller->record->getXref(), 0, true, 5); // footnotes
echo '</td>';
echo '</tr></table>';
