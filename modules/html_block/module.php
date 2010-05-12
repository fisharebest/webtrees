<?php
/**
 * Classes and libraries for module system
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2010 John Finlay
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
 * @version $Id: class_media.php 5451 2009-05-05 22:15:34Z fisharebest $
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

require_once WT_ROOT.'includes/classes/class_module.php';

class html_block_WT_Module extends WT_Module implements WT_Module_Block {
	// Extend class WT_Module
	public function getTitle() {
		return i18n::translate('HTML');
	}

	// Extend class WT_Module
	public function getDescription() {
		return i18n::translate('This is a simple HTML block that you can place on your page to add any sort of message you may want.');
	}

	// Implement class WT_Module_Block
	public function getBlock($block_id) {
		global $ctype, $WT_IMAGE_DIR, $WT_IMAGES, $THEME_DIR;

		$id=$this->getName().$block_id;
		$title='';
		$content=embed_globals(get_block_setting($block_id, 'html'));

		if ($ctype=="gedcom" && WT_USER_GEDCOM_ADMIN || $ctype=="user") {
			$content .= "<br /><a href=\"javascript:;\" onclick=\"window.open('".encode_url("index_edit.php?action=configure&block_id={$block_id}")."', '_blank', 'top=50,left=50,width=600,height=350,scrollbars=1,resizable=1'); return false;\">";
			$content .= "<img class=\"adminicon\" src=\"$WT_IMAGE_DIR/".$WT_IMAGES["admin"]["small"]."\" width=\"15\" height=\"15\" border=\"0\" alt=\"".i18n::translate('Configure')."\" title=\"".i18n::translate('Configure')."\" /></a>\n";
		}

		$block=get_block_setting($block_id, 'block', false);
		if ($block) {
			require $THEME_DIR.'templates/block_small_temp.php';
		} else {
			require $THEME_DIR.'templates/block_main_temp.php';
		}
	}

	// Implement class WT_Module_Block
	public function loadAjax() {
		return true;
	}

	// Implement class WT_Module_Block
	public function isUserBlock() {
		return true;
	}

	// Implement class WT_Module_Block
	public function isGedcomBlock() {
		return true;
	}

	// Implement class WT_Module_Block
	public function configureBlock($block_id) {
		if (safe_POST_bool('save')) {
			set_block_setting($block_id, 'html', $_POST['html']);
			echo WT_JS_START, 'window.opener.location.href=window.opener.location.href;window.close();', WT_JS_END;
			exit;
		}

		$useFCK = file_exists(WT_ROOT.'modules/FCKeditor/fckeditor.php');
		if($useFCK){
			require WT_ROOT.'modules/FCKeditor/fckeditor.php';
		}

?>
		<tr>
		<td class="optionbox" colspan="2"><?php
		if ($useFCK) { // use FCKeditor module
			$oFCKeditor = new FCKeditor('html') ;
			$oFCKeditor->BasePath =  './modules/FCKeditor/';
			$oFCKeditor->Value = get_block_setting($block_id, 'html');
			$oFCKeditor->Width = 700;
			$oFCKeditor->Height = 250;
			$oFCKeditor->Config['AutoDetectLanguage'] = false ;
			$oFCKeditor->Config['DefaultLanguage'] = WT_LOCALE;
			$oFCKeditor->Create() ;
		} else { //use standard textarea
			echo '<textarea name="html" rows="10" cols="80">', htmlspecialchars(get_block_setting($block_id, 'html')), '</textarea>';
		}
		?></td>
		</tr>
		<?php
	}
}
