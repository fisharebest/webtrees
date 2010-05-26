<?php
/**
 * Module to display a favorite person on the welcome page.
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
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
 * @version $Id$
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

require_once WT_ROOT.'includes/classes/class_module.php';

class favorite_WT_Module extends WT_Module implements WT_Module_Block {
	// Extend class WT_Module
	public function getTitle() {
		return i18n::translate('Favorite');
	}

	// Extend class WT_Module
	public function getDescription() {
		return i18n::translate('Add a block containing details of a favorite person.');
	}

	// Implement class WT_Module_Block
	public function getBlock($block_id) {
		global $THEME_DIR, $ctype;

		if (safe_POST('save_block')==$block_id) {
			set_block_setting($block_id, 'title',      safe_POST('title'));
			set_block_setting($block_id, 'url',        safe_POST('url'));
			set_block_setting($block_id, 'gedcom_id',  safe_POST('gedcom_id'));
			set_block_setting($block_id, 'xref',       safe_POST('xref'));
			set_block_setting($block_id, 'note',       safe_POST('note'));
			// Reload the page, otherwise the browser may try to re-post these
			// variables when we reload the page.
			echo WT_JS_START.'window.location.href="index.php?ctype='.$ctype.'";'.WT_JS_END;
			return;
		}

		$title    =get_block_setting($block_id, 'title');
		$url      =get_block_setting($block_id, 'url');
		$gedcom_id=get_block_setting($block_id, 'gedcom_id', WT_GED_ID);
		$xref     =get_block_setting($block_id, 'xref');
		$note     =get_block_setting($block_id, 'note');

		$id=$this->getName().$block_id;

		if ($url) {
			if ($title) {
				$title='<a href="'.urlencode($url).'">'.htmlspecialchars($title).'</a>';
			} else {
				$title='<a href="'.urlencode($url).'">'.htmlspecialchars($url).'</a>';
			}
		}

		if ($gedcom_id && $xref) {
			$GEDCOM=get_gedcom_from_id($gedcom_id);
			$record=Person::getInstance($xref);
			if ($record) {
				// We need a better way to generate these.  ob() and globals suck.
				ob_start();
				global $show_full; $show_full=1;
				print_pedigree_person($xref, 2, false);
				$content=ob_get_clean();
			} else {
				$content=i18n::translate('Record %s not found', $xref);
			}
		} else {
			$content='';
		}

		if ($note) {
			$content.='<p>'.htmlspecialchars($note).'</p>';
		}

		// No content?  Must be a new block.  Edit it if we can.
		if (!$content && !$title && ($ctype=='user' && WT_USER_ID || $ctype=='gedcom' && WT_USER_GEDCOM_ADMIN)) {
			$content=
				'<form method="post" action="#">'.
				'<input type="hidden" name="save_block" value="'.$block_id.'" />'.
				'<table border="0" class="facts_table">'.
				'<tr><td class="descriptionbox wrap width33">'.
				i18n::translate('Add an optional title to this favorite').
				'</td><td class="optionbox">'.
				'<input name="title" value="'.htmlspecialchars($title).'" size="40" />'.
				'</td></tr>'.

				'<tr><td class="descriptionbox wrap width33">'.
				i18n::translate('Add an optional URL').
				'</td><td class="optionbox">'.
				'<input name="url" value="'.htmlspecialchars($url).'" size="40" />'.
				'</td></tr>'.

				'<tr><td class="descriptionbox wrap width33">'.
				i18n::translate('Select a person or other record').
				'</td><td class="optionbox">'.
				'<input name="xref" value="'.htmlspecialchars($xref).'" size="4" />'.
				'<input name="gedcom_id" value="'.WT_GED_ID.'" type="hidden" />'.
				'</td></tr>'.

				'<tr><td class="descriptionbox wrap width33">'.
				i18n::translate('Add an optional note to this favorite').
				'</td><td class="optionbox">'.
				'<textarea name="note" rows="5" cols="40">'.htmlspecialchars($note).'</textarea>'.
				'</td></tr>'.
				'</table>'.
				'<input type="submit" value="'.i18n::translate('Save').'" />';
				'</form>';
		}
		
		require $THEME_DIR.'templates/block_main_temp.php';
	}

	// Implement class WT_Module_Block
	public function loadAjax() {
		return false;
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
	}
}
