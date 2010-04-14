<?php
/**
 * Advanced HTML Block
 *
 * This block will print advanced HTML text with keyword support entered by an admin
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
 * @author Patrick Kellum
 * @package webtrees
 * @subpackage Blocks
 * @version $Id$
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('WT_BLOCK_HTMLPLUS_PHP', '');

require_once WT_ROOT.'includes/functions/functions_print_lists.php';
require_once WT_ROOT.'includes/classes/class_stats.php';

$WT_BLOCKS['print_htmlplus_block']=array(
	'name'=>i18n::translate('Advanced HTML'),
	'type'=>'both',
	'descr'=>i18n::translate('This is an HTML block that you can place on your page to add any sort of message you may want.  You can insert references to information from your GEDCOM into the HTML text.'),
	'canconfig'=>true,
	'config'=>array(
		'cache'=>0,
		'title'=>'',
		'html'=>i18n::translate('<p class="blockhc"><b>Put your title here</b></p><br /><p>Click the configure button')." <img src=\"{$WT_IMAGE_DIR}/{$WT_IMAGES['admin']['small']}\" alt=\"".i18n::translate('Configure')."\" /> ".i18n::translate('to change what is printed here.</p>'),
		'gedcom'=>'__current__',
		'compat'=>0,
		'ui'=>0
	)
);

function print_htmlplus_block($block=true, $config='', $side, $index) {
	global $ctype, $GEDCOM, $HTML_BLOCK_COUNT, $WT_BLOCKS, $WT_IMAGE_DIR, $WT_IMAGES, $TEXT_DIRECTION, $MULTI_MEDIA, $SHOW_ID_NUMBERS;
	// config sanity check
	if (empty($config)){$config = $WT_BLOCKS['print_htmlplus_block']['config'];}else{foreach($WT_BLOCKS['print_htmlplus_block']['config'] as $k=>$v){if (!isset($config[$k])){$config[$k] = $v;}}}

	if (!isset($HTML_BLOCK_COUNT)){$HTML_BLOCK_COUNT = 0;}$HTML_BLOCK_COUNT++;

	/*
	 * Select GEDCOM
	 */
	switch($config['gedcom']) {
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
		if (get_gedcom_setting(get_gedcom_from_id($config['gedcom']), 'imported')) {
			$GEDCOM = $config['gedcom'];
		}
		break;
	}

	/*
	 * Initiate the stats object.
	 */
	if($config['compat'] == 1)
	{
		require_once WT_ROOT.'includes/classes/class_stats_compat.php';
		$stats = new stats_compat($GEDCOM);
	}
	elseif($config['ui'] == 1)
	{
		require_once WT_ROOT.'includes/classes/class_stats_ui.php';
		$stats = new stats_ui($GEDCOM);
	}
	else
	{
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
	$config['title'] = embed_globals($config['title']);
	// Content
	$config['html'] = embed_globals($config['html']);

	/*
	 * Second Pass.
	 */
	list($new_tags, $new_values) = $stats->getTags("{$config['title']} {$config['html']}");
	// Title
	if (strstr($config['title'], '#')){$config['title'] = str_replace($new_tags, $new_values, $config['title']);}
	// Content
	$config['html'] = str_replace($new_tags, $new_values, $config['html']);

	/*
	 * Restore Current GEDCOM
	 */
	$GEDCOM = WT_GEDCOM;

	/*
	 * Start Of Output
	 */
	$id = "html_block{$HTML_BLOCK_COUNT}";
	$title = "";
	if ($config['title'] != '') {
		if ($WT_BLOCKS['print_htmlplus_block']['canconfig']) {
			if ($ctype=="gedcom" && WT_USER_GEDCOM_ADMIN || $ctype=="user" && WT_USER_ID) {
				if ($ctype=="gedcom") {
					$name = WT_GEDCOM;
				} else {
					$name = WT_USER_NAME;
				}
				$title .= "<a href=\"javascript: configure block\" onclick=\"window.open('".encode_url("index_edit.php?name={$name}&ctype={$ctype}&action=configure&side={$side}&index={$index}")."', '_blank', 'top=50,left=50,width=600,height=350,scrollbars=1,resizable=1'); return false;\">"
				."<img class=\"adminicon\" src=\"{$WT_IMAGE_DIR}/{$WT_IMAGES['admin']['small']}\" width=\"15\" height=\"15\" border=\"0\" alt=\"".i18n::translate('Configure').'" /></a>'
				;
			}
		}
		$title .= $config['title'];
	}
	if (WT_USER_GEDCOM_ADMIN) {
		$title .= help_link('index_htmlplus_a');
	} else {
		$title .= help_link('index_htmlplus');
	}

	$content = $config['html'];
	if ($config['title'] == '' && $WT_BLOCKS['print_htmlplus_block']['canconfig']) {
		if ($ctype=="gedcom" && WT_USER_GEDCOM_ADMIN || $ctype=="user" && WT_USER_ID) {
			if ($ctype=="gedcom") {
				$name = str_replace("'", "\'", $GEDCOM);
			} else {
				$name = WT_USER_NAME;
			}
			$content .= "<br />"
			."<a href=\"javascript:;\" onclick=\"window.open('".encode_url("index_edit.php?name={$name}&ctype={$ctype}&action=configure&side={$side}&index={$index}")."', '_blank', 'top=50,left=50,width=600,height=500,scrollbars=1,resizable=1'); return false;\">"
			."<img class=\"adminicon\" src=\"{$WT_IMAGE_DIR}/{$WT_IMAGES['admin']['small']}\" width=\"15\" height=\"15\" border=\"0\" alt=\"".i18n::translate('Configure')."\" title=\"".i18n::translate('Configure')."\" /></a>"
			.help_link('index_htmlplus_ahelp');
		}
	}

	global $THEME_DIR;
	if ($block) {
		require $THEME_DIR.'templates/block_small_temp.php';
	} else {
		require $THEME_DIR.'templates/block_main_temp.php';
	}
}

function print_htmlplus_block_config($config)
{
	global $ctype, $WT_BLOCKS, $TEXT_DIRECTION, $GEDCOM;
	$useFCK = file_exists(WT_ROOT.'modules/FCKeditor/fckeditor.php');
	$templates = array();
	$d = dir('blocks/');
	while(false !== ($entry = $d->read()))
	{
		if(strstr($entry, 'block_htmlplus_'))
		{
			$tpl = file("blocks/{$entry}");
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

	// config sanity check
	if(empty($config)){$config = $WT_BLOCKS['print_htmlplus_block']['config'];}else{foreach($WT_BLOCKS['print_htmlplus_block']['config'] as $k=>$v){if (!isset($config[$k])){$config[$k] = $v;}}}

	// title
	$config['title'] = htmlentities($config['title'], ENT_COMPAT, 'UTF-8');
	print "<tr><td class=\"descriptionbox wrap width33\">"
		.i18n::translate('TITL')
		.help_link('index_htmlplus_title')
		."</td><td class=\"optionbox\"><input type=\"text\" name=\"title\" size=\"30\" value=\"{$config['title']}\" /></td></tr>"
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
	if(count($gedcoms) > 1)
	{
		if($config['gedcom'] == '__current__'){$sel_current = ' selected="selected"';}else{$sel_current = '';}
		if($config['gedcom'] == '__default__'){$sel_default = ' selected="selected"';}else{$sel_default = '';}
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
			if($ged_name == $config['gedcom']){$sel = ' selected="selected"';}else{$sel = '';}
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
		$oFCKeditor->Value = $config['html'];
		$oFCKeditor->Width = 700;
		$oFCKeditor->Height = 250;
		$oFCKeditor->Config['AutoDetectLanguage'] = false ;
		$oFCKeditor->Config['DefaultLanguage'] = WT_LOCALE;
		$oFCKeditor->Create() ;
	}
	else
	{
		//use standard textarea
		print "<textarea name=\"html\" rows=\"10\" cols=\"80\">".str_replace("<", "&lt;", $config['html'])."</textarea>";
	}

	print "\n\t\t</td>\n\t</tr>\n";

	// compatibility mode
	if($config['compat'] == 1){$compat = ' checked="checked"';}else{$compat = '';}
	print "\t<tr>\n\t\t<td class=\"descriptionbox wrap width33\">"
		.i18n::translate('Compatibility Mode')
		.help_link('index_htmlplus_compat')
		."</td>\n<td class=\"optionbox\"><input type=\"checkbox\" name=\"compat\" value=\"1\"{$compat} /></td>\n"
		."\t</tr>\n"
	;

	// extended features
	if ($config['ui'] == 1) {
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

	// Cache file life
	if($ctype == 'gedcom')
	{
		print "\t<tr>\n\t\t<td class=\"descriptionbox wrap width33\">"
			.i18n::translate('Cache file life')
			.help_link('cache_life')
			."</td><td class=\"optionbox\">"
			."<input type=\"text\" name=\"cache\" size=\"2\" value=\"{$config['cache']}\" /></td>\n"
			."\t</tr>\n"
		;
	}
}
