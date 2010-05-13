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

class todays_events_WT_Module extends WT_Module implements WT_Module_Block {
	// Extend class WT_Module
	public function getTitle() {
		return i18n::translate('On This Day');
	}

	// Extend class WT_Module
	public function getDescription() {
		return i18n::translate('The On This Day, in Your History... block shows anniversaries of events for today.  You can configure the amount of detail shown.');
	}

	// Implement class WT_Module_Block
	public function getBlock($block_id) {
		global $SHOW_ID_NUMBERS, $ctype, $TEXT_DIRECTION, $WT_IMAGE_DIR, $WT_IMAGES, $DAYS_TO_SHOW_LIMIT, $THEME_DIR;

		$filter       =get_block_setting($block_id, 'days', 'all');
		$onlyBDM      =get_block_setting($block_id, 'days', 'no');
		$infoStyle    =get_block_setting($block_id, 'days', 'style2');
		$sortStyle    =get_block_setting($block_id, 'days', 'alpha');
		$allowDownload=WT_USER_ID && get_block_setting($block_id, 'allowDownload', true);

		$todayjd=client_jd();

		$id=$this->getName().$block_id;
		$title='';
		if ($ctype=="gedcom" && WT_USER_GEDCOM_ADMIN || $ctype=="user" && WT_USER_ID) {
			if ($ctype=="gedcom") {
				$name = WT_GEDCOM;
			} else {
				$name = WT_USER_NAME;
			}
	  	$title .= "<a href=\"javascript: configure block\" onclick=\"window.open('index_edit.php?action=configure&amp;ctype={$ctype}&amp;block_id={$block_id}', '_blank', 'top=50,left=50,width=600,height=350,scrollbars=1,resizable=1'); return false;\">";
	  	$title .= "<img class=\"adminicon\" src=\"$WT_IMAGE_DIR/".$WT_IMAGES["admin"]["small"]."\" width=\"15\" height=\"15\" border=\"0\" alt=\"".i18n::translate('Configure')."\" /></a>";
  	}
  	$title.=i18n::translate('On This Day ...').help_link('index_onthisday');

  	$content = "";
		switch ($infoStyle) {
		case "style1":
			// Output style 1:  Old format, no visible tables, much smaller text.  Better suited to right side of page.
			$content .= print_events_list($todayjd, $todayjd, $onlyBDM=='yes'?'BIRT MARR DEAT':'', $filter=='living', $sortStyle);
			break;
		case "style2":
			// Style 2: New format, tables, big text, etc.  Not too good on right side of page
			ob_start();
			$content .= print_events_table($todayjd, $todayjd, $onlyBDM=='yes'?'BIRT MARR DEAT':'', $filter=='living', $allowDownload=='yes', $sortStyle);
			$content .= ob_get_clean();
			break;
		}

		$block=get_block_setting($block_id, 'block', true);
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
			set_block_setting($block_id, 'filter',        safe_POST_bool('filter'));
			set_block_setting($block_id, 'onlyBDM',       safe_POST_bool('onlyBDM'));
			set_block_setting($block_id, 'infoStyle',     safe_POST('infoStyle', array('list', 'table'), 'table'));
			set_block_setting($block_id, 'sortStyle',     safe_POST('sortStyle', array('alpha', 'anniv'), 'alpha'));
			set_block_setting($block_id, 'allowDownload', safe_POST_bool('allowDownload'));
			set_block_setting($block_id, 'block',  safe_POST_bool('block'));
			echo WT_JS_START, 'window.opener.location.href=window.opener.location.href;window.close();', WT_JS_END;
			exit;
		}

		$filter       =get_block_setting($block_id, 'days', 'all');
		$onlyBDM      =get_block_setting($block_id, 'days', 'no');
		$infoStyle    =get_block_setting($block_id, 'days', 'style2');
		$sortStyle    =get_block_setting($block_id, 'days', 'alpha');
		$allowDownload=get_block_setting($block_id, 'allowDownload', true);

		require_once WT_ROOT.'includes/functions/functions_edit.php';
		
		?>
		<tr><td class="descriptionbox wrap width33">
		<?php
		print i18n::translate('Show only events of living people?');
		?>
		</td><td class="optionbox">
			<select name="filter">
				<option value="all"<?php if ($filter=="all") print " selected=\"selected\"";?>><?php print i18n::translate('No'); ?></option>
				<option value="living"<?php if ($filter=="living") print " selected=\"selected\"";?>><?php print i18n::translate('Yes'); ?></option>
			</select>
		</td></tr>

		<tr><td class="descriptionbox wrap width33">
		<?php
		print i18n::translate('Show only Births, Deaths, and Marriages?');
		print help_link('basic_or_all');
		?>
		</td><td class="optionbox">
			<select name="onlyBDM">
				<option value="no"<?php if (!$onlyBDM) print " selected=\"selected\"";?>><?php print i18n::translate('No'); ?></option>
				<option value="yes"<?php if ($onlyBDM) print " selected=\"selected\"";?>><?php print i18n::translate('Yes'); ?></option>
			</select>
		</td></tr>

		<tr><td class="descriptionbox wrap width33">
		<?php
		print i18n::translate('Presentation Style');
		print help_link('style');
		?>
		</td><td class="optionbox">
			<select name="infoStyle">
				<option value="style1"<?php if ($infoStyle=="list") print " selected=\"selected\"";?>><?php print i18n::translate('List'); ?></option>
				<option value="style2"<?php if ($infoStyle=="table") print " selected=\"selected\"";?>><?php print i18n::translate('Table'); ?></option>
			</select>
		</td></tr>

		<tr><td class="descriptionbox wrap width33">
		<?php
		print i18n::translate('Sort Style');
		print help_link('sort_style');
		?>
		</td><td class="optionbox">
			<select name="sortStyle">
				<option value="alpha"<?php if ($sortStyle=="alpha") print " selected=\"selected\"";?>><?php print i18n::translate('Alphabetically'); ?></option>
				<option value="anniv"<?php if ($sortStyle=="anniv") print " selected=\"selected\"";?>><?php print i18n::translate('By Anniversary'); ?></option>
			</select>
		</td></tr>

		<tr><td class="descriptionbox wrap width33">
		<?php
		print i18n::translate('Allow calendar events download?');
		print help_link('cal_dowload');
		?>
		</td><td class="optionbox">
			<select name="allowDownload">
				<option value="yes"<?php if ($allowDownload) print " selected=\"selected\"";?>><?php print i18n::translate('Yes'); ?></option>
				<option value="no"<?php if (!$allowDownload) print " selected=\"selected\"";?>><?php print i18n::translate('No'); ?></option>
			</select>
			<input type="hidden" name="cache" value="1" />
		</td></tr>
	  <?php

		$block=get_block_setting($block_id, 'block', true);
		echo '<tr><td class="descriptionbox wrap width33">';
		echo i18n::translate('Add a scrollbar when block contents grow');
		echo '</td><td class="optionbox">';
		echo edit_field_yes_no('block', $block);
		echo '</td></tr>';
	}
}
