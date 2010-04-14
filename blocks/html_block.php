<?php
/**
 * Simple HTML Block
 *
 * This block will print simple HTML text entered by an admin
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
 * @subpackage Blocks
 * @version $Id$
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('WT_HTML_BLOCK_PHP', '');

$WT_BLOCKS['print_html_block']=array(
	'name'=>i18n::translate('HTML'),
	'type'=>'both',
	'descr'=>i18n::translate('This is a simple HTML block that you can place on your page to add any sort of message you may want.'),
	'canconfig'=>true,
	'config'=>array(
		'cache'=>1,
		'html'=>i18n::translate('<p class="blockhc"><b>Put your title here</b></p><br /><p>Click the configure button')." <img src=\"".$WT_IMAGE_DIR.'/'.$WT_IMAGES['admin']['small']."\" alt=\"".i18n::translate('Configure')."\" /> ".i18n::translate('to change what is printed here.</p>')
	)
);

function print_html_block($block=true, $config="", $side, $index) {
	global $WT_IMAGE_DIR, $TEXT_DIRECTION, $WT_IMAGES, $HTML_BLOCK_COUNT, $WT_BLOCKS, $ctype;

	if (empty($config)) $config = $WT_BLOCKS["print_html_block"]["config"];
	if (!isset($HTML_BLOCK_COUNT)) $HTML_BLOCK_COUNT = 0;
	$HTML_BLOCK_COUNT++;

	$id = "html_block$HTML_BLOCK_COUNT";
	$title = "";
	$content = embed_globals($config['html']);

	if ($WT_BLOCKS["print_html_block"]["canconfig"]) {
		if ($ctype=="gedcom" && WT_USER_GEDCOM_ADMIN || $ctype=="user" && WT_USER_ID) {
			if ($ctype=="gedcom") {
				$name = WT_GEDCOM;
			} else {
				$name = WT_USER_NAME;
			}
			$content .= "<br /><a href=\"javascript:;\" onclick=\"window.open('".encode_url("index_edit.php?name={$name}&ctype={$ctype}&action=configure&side={$side}&index={$index}")."', '_blank', 'top=50,left=50,width=600,height=350,scrollbars=1,resizable=1'); return false;\">";
			$content .= "<img class=\"adminicon\" src=\"$WT_IMAGE_DIR/".$WT_IMAGES["admin"]["small"]."\" width=\"15\" height=\"15\" border=\"0\" alt=\"".i18n::translate('Configure')."\" title=\"".i18n::translate('Configure')."\" /></a>\n";
		}
	}

	global $THEME_DIR;
	if ($block) {
		require $THEME_DIR.'templates/block_small_temp.php';
	} else {
		require $THEME_DIR.'templates/block_main_temp.php';
	}
}

function print_html_block_config($config) {
	global $ctype, $WT_BLOCKS, $TEXT_DIRECTION;
	$useFCK = file_exists(WT_ROOT.'modules/FCKeditor/fckeditor.php');
	if($useFCK){
		require WT_ROOT.'modules/FCKeditor/fckeditor.php';
	}
	if (empty($config)) $config = $WT_BLOCKS["print_html_block"]["config"];
	if (empty($config["cache"])) $config["cache"] = $WT_BLOCKS["print_html_block"]["config"]["cache"];
	?>
	<tr>
	<td class="optionbox" colspan="2"><?php
	if ($useFCK) { // use FCKeditor module
		$oFCKeditor = new FCKeditor('html') ;
		$oFCKeditor->BasePath =  './modules/FCKeditor/';
		$oFCKeditor->Value = $config["html"];
		$oFCKeditor->Width = 700;
		$oFCKeditor->Height = 250;
		$oFCKeditor->Config['AutoDetectLanguage'] = false ;
		$oFCKeditor->Config['DefaultLanguage'] = WT_LOCALE;
		$oFCKeditor->Create() ;
	} else { //use standard textarea
		print "<textarea name=\"html\" rows=\"10\" cols=\"80\">" . str_replace("<", "&lt;", $config["html"]) ."</textarea>";
	}
	?></td>
	</tr>
	<?php
	// Cache file life
	if ($ctype=="gedcom") {
  	echo "<tr><td class=\"descriptionbox wrap width33\">";
		echo i18n::translate('Cache file life'), help_link('cache_life');
		echo "</td><td class=\"optionbox\">";
		echo "<input type=\"text\" name=\"cache\" size=\"2\" value=\"".$config["cache"]."\" />";
		echo "</td></tr>";
	}
}
?>
