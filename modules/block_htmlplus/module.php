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

class block_htmlplus_WT_Module extends WT_Module implements WT_Module_Block {
	// Extend class WT_Module
	public function getTitle() {
		return i18n::translate('Advanced HTML');
	}

	// Extend class WT_Module
	public function getDescription() {
		return i18n::translate('This is an HTML block that you can place on your page to add any sort of message you may want.  You can insert references to information from your GEDCOM into the HTML text.');
	}

	// Implement class WT_Module_Block
	public function getBlock($block_id) {
		global $ctype, $GEDCOM, $WT_IMAGE_DIR, $WT_IMAGES, $TEXT_DIRECTION, $MULTI_MEDIA, $SHOW_ID_NUMBERS, $THEME_DIR;

		/*
	 	* Select GEDCOM
	 	*/
		$gedcom=get_block_setting($block_id, 'gedcom');
		switch($gedcom) {
		case '__current__':
			break;
		case '':
			break;
		case '__default__':
			$GEDCOM=get_site_setting('DEFAULT_GEDCOM');
			if (!$GEDCOM) {
				foreach (get_all_gedcoms() as $gedcom) {
					$GEDCOM=$gedcom;
					break;
				}
			}
			break;
		default:
			if (get_gedcom_setting(get_gedcom_from_id($gedcom), 'imported')) {
				$GEDCOM = $gedcom;
			}
			break;
		}

		/*
	 	* Initiate the stats object.
	 	*/
		if (get_block_setting($block_id, 'compat')) {
			require_once WT_ROOT.'includes/classes/class_stats_compat.php';
			$stats = new stats_compat($GEDCOM);
		} elseif(get_block_setting($block_id, 'ui')) {
			require_once WT_ROOT.'includes/classes/class_stats_ui.php';
			$stats = new stats_ui($GEDCOM);
		} else {
			require_once WT_ROOT.'includes/classes/class_stats.php';
			$stats = new stats($GEDCOM);
		}

		// Make some values from the GEDCOM's 0 HEAD record visible to the world
		global $CREATED_SOFTWARE, $CREATED_VERSION, $CREATED_DATE;
		$CREATED_SOFTWARE = $stats->gedcomCreatedSoftware();
		$CREATED_VERSION = $stats->gedcomCreatedVersion();
		$CREATED_DATE = $stats->gedcomDate();

		/*
	 	* First Pass.
	 	* Handle embedded language, fact, global, etc. references
	 	*   This needs to be done first because the language variables could themselves
	 	*   contain embedded keywords.
	 	*/
		// Title
		$title_tmp=embed_globals(get_block_setting($block_id, 'title'));
		$html =embed_globals(get_block_setting($block_id, 'html'));
		/*
	 	* Second Pass.
	 	*/
		list($new_tags, $new_values) = $stats->getTags("{$title_tmp} {$html}");
		// Title
		if (strstr($title_tmp, '#')){$title_tmp = str_replace($new_tags, $new_values, $title_tmp);}
		// Content
		$html = str_replace($new_tags, $new_values, $html);

		/*
	 	* Restore Current GEDCOM
	 	*/
		$GEDCOM = WT_GEDCOM;

		/*
	 	* Start Of Output
		*/
		$id=$this->getName().$block_id;
		$title='';
		if ($ctype=="gedcom" && WT_USER_GEDCOM_ADMIN || $ctype=="user" && WT_USER_ID) {
			if ($ctype=="gedcom") {
				$name = WT_GEDCOM;
			} else {
				$name = WT_USER_NAME;
			}
			$title .= "<a href=\"javascript: configure block\" onclick=\"window.open('index_edit.php?action=configure&amp;ctype={$ctype}&amp;block_id={$block_id}', '_blank', 'top=50,left=50,width=600,height=350,scrollbars=1,resizable=1'); return false;\">"
			."<img class=\"adminicon\" src=\"{$WT_IMAGE_DIR}/{$WT_IMAGES['admin']['small']}\" width=\"15\" height=\"15\" border=\"0\" alt=\"".i18n::translate('Configure').'" /></a>';
			;
		}
		if (WT_USER_GEDCOM_ADMIN) {
			$title .= help_link('index_htmlplus_a');
		} else {
			$title .= help_link('index_htmlplus');
		}
		$title.=$title_tmp;

		$content = $html;

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
			set_block_setting($block_id, 'compat', safe_POST_bool('compat'));
			set_block_setting($block_id, 'ui', safe_POST_bool('ui'));
			set_block_setting($block_id, 'gedcom', safe_POST('gedcom'));
			set_block_setting($block_id, 'title', $_POST['title']);
			set_block_setting($block_id, 'html', $_POST['html']);
			echo WT_JS_START, 'window.opener.location.href=window.opener.location.href;window.close();', WT_JS_END;
			exit;
		}

		$useFCK = file_exists(WT_ROOT.'modules/FCKeditor/fckeditor.php');
		if($useFCK){
			require WT_ROOT.'modules/FCKeditor/fckeditor.php';
		}

		$templates = array();
		$d = dir(WT_ROOT.'modules/block_htmlplus');
		while(false !== ($entry = $d->read()))
		{
			if(strstr($entry, 'block_htmlplus_'))
			{
				$tpl = file(WT_ROOT."modules/block_htmlplus/{$entry}");
				$info = array_shift($tpl);
				$bits = explode('|', $info);
				if(count($bits) != 2)
				{
					$bits = array($entry, '');
				}
				$templates[] = array(
					'filename'		=>$entry,
					'title'			=>$bits[0],
					'description'	=>$bits[1],
					'template'		=>htmlspecialchars(join('', $tpl),ENT_COMPAT,'UTF-8')
				);
			}
		}
		$d->close();

		$title=get_block_setting($block_id, 'title');
		$html=get_block_setting($block_id, 'html');
		// title
		print "<tr><td class=\"descriptionbox wrap width33\">"
			.translate_fact('TITL')
			.help_link('index_htmlplus_title')
			."</td><td class=\"optionbox\"><input type=\"text\" name=\"title\" size=\"30\" value=\"".htmlspecialchars($title)."\" /></td></tr>"
		;

		// templates
		print "<tr><td class=\"descriptionbox wrap width33\">"
			.i18n::translate('Templates')
			.help_link('index_htmlplus_template')
			."</td><td class=\"optionbox\">"
		;
		if($useFCK)
		{
			print "\t\t\t<script language=\"JavaScript\" type=\"text/javascript\">\n"
				."\t\t\t<!--\n"
				."\t\t\t\tfunction loadTemplate(html)\n"
				."\t\t\t\t{\n"
				."\t\t\t\t\tvar oEditor = FCKeditorAPI.GetInstance('html');\n"
				."\t\t\t\t\toEditor.SetHTML(html);\n"
				."\t\t\t\t}\n"
				."\t\t\t-->\n"
				."\t\t\t</script>\n"
				."\t\t\t<select name=\"template\" onchange=\"loadTemplate(document.block.template.options[document.block.template.selectedIndex].value);\">\n"
			;
		}
		else
		{
			print "\t\t\t<select name=\"template\" onchange=\"document.block.html.value=document.block.template.options[document.block.template.selectedIndex].value;\">\n";
		}
		print "\t\t\t\t<option value=\"\">".i18n::translate('Custom')."</option>\n";
		foreach($templates as $tpl)
		{
			print "\t\t\t\t<option value=\"{$tpl['template']}\">{$tpl['title']}</option>\n";
		}
		print "\t\t\t</select>\n"
			."\t\t</td>\n\t</tr>\n"
		;

		// gedcom
		$gedcoms = get_all_gedcoms();
		$gedcom=get_block_setting($block_id, 'gedcom');
		if(count($gedcoms) > 1)
		{
			if($gedcom == '__current__'){$sel_current = ' selected="selected"';}else{$sel_current = '';}
			if($gedcom == '__default__'){$sel_default = ' selected="selected"';}else{$sel_default = '';}
			print "\t<tr>\n\t\t<td class=\"descriptionbox wrap width33\">"
				.i18n::translate('Family Tree')
				.help_link('index_htmlplus_gedcom')
				."</td><td class=\"optionbox\">\n"
				."\t\t\t<select name=\"gedcom\">\n"
				."\t\t\t\t<option value=\"__current__\"{$sel_current}>".i18n::translate('Current')."</option>\n"
				."\t\t\t\t<option value=\"__default__\"{$sel_default}>".i18n::translate('Default')."</option>\n"
			;
			foreach($gedcoms as $ged_id=>$ged_name)
			{
				if($ged_name == $gedcom){$sel = ' selected="selected"';}else{$sel = '';}
				print "\t\t\t\t<option value=\"{$ged_name}\"{$sel}>".PrintReady(get_gedcom_setting($ged_id, 'title'))."</option>\n";
			}
			print "\t\t\t</select>\n"
				."\t\t</td>\n\t</tr>\n"
			;
		}

		// html
		print "\t<tr>\n\t\t<td class=\"descriptionbox wrap width33\">\n"
			.i18n::translate('Content')
			.help_link('index_htmlplus_content')
			."<br /><br /></td>"
			."<td class=\"optionbox\">"
		;
		if($useFCK)
		{
			// use FCKeditor module
			require_once WT_ROOT.'modules/FCKeditor/fckeditor.php';
			$oFCKeditor = new FCKeditor('html') ;
			$oFCKeditor->BasePath = './modules/FCKeditor/';
			$oFCKeditor->Value = $html;
			$oFCKeditor->Width = 700;
			$oFCKeditor->Height = 250;
			$oFCKeditor->Config['AutoDetectLanguage'] = false ;
			$oFCKeditor->Config['DefaultLanguage'] = WT_LOCALE;
			$oFCKeditor->Create() ;
		}
		else
		{
			//use standard textarea
			print "<textarea name=\"html\" rows=\"10\" cols=\"80\">".htmlspecialchars($html)."</textarea>";
		}

		print "\n\t\t</td>\n\t</tr>\n";

		// compatibility mode
		$compat=get_block_setting($block_id, 'compat', false);
		if($compat == 1){$compat = ' checked="checked"';}else{$compat = '';}
		print "\t<tr>\n\t\t<td class=\"descriptionbox wrap width33\">"
			.i18n::translate('Compatibility Mode')
			.help_link('index_htmlplus_compat')
			."</td>\n<td class=\"optionbox\"><input type=\"checkbox\" name=\"compat\" value=\"1\"{$compat} /></td>\n"
			."\t</tr>\n"
		;

		// extended features
		$ui=get_block_setting($block_id, 'ui', false);
		if ($ui == 1) {
			$ui = ' checked="checked"';
		} else {
			$ui = '';
		}
		print "\t<tr>\n\t\t<td class=\"descriptionbox wrap width33\">"
			.i18n::translate('Extended Interface')
			.help_link('index_htmlplus_ui')
			."</td><td class=\"optionbox\"><input type=\"checkbox\" name=\"ui\" value=\"1\"{$ui} /></td>\n"
			."\t</tr>\n"
		;
	}
}
