<?php
// Classes and libraries for module system
//
// webtrees: Web based Family History software
// Copyright (C) 2011 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2010 John Finlay
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
// @version $Id$

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class html_WT_Module extends WT_Module implements WT_Module_Block {
	// Extend class WT_Module
	public function getTitle() {
		return WT_I18N::translate('HTML');
	}

	// Extend class WT_Module
	public function getDescription() {
		return WT_I18N::translate('This is an HTML block that you can place on your page to add any sort of message you may want.  You can insert references to information from your GEDCOM into the HTML text.');
	}

	// Implement class WT_Module_Block
	public function getBlock($block_id, $template=true, $cfg=null) {
		global $ctype, $GEDCOM, $WT_IMAGES, $TEXT_DIRECTION, $MULTI_MEDIA;

		// Only show this block for certain languages
		$languages=get_block_setting($block_id, 'languages');
		if ($languages && !in_array(WT_LOCALE, explode(',', $languages))) {
			return;
		}

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
		$stats = new WT_Stats($GEDCOM);

		/*
		* First Pass.
		* Handle embedded language, fact, global, etc. references
		*   This needs to be done first because the language variables could themselves
		*   contain embedded keywords.
		*/
		// Title
		$title_tmp=get_block_setting($block_id, 'title');
		$html =get_block_setting($block_id, 'html');
		/*
		* Second Pass.
		*/
		list($new_tags, $new_values) = $stats->getTags("{$title_tmp} {$html}");
		// Title
		if (strstr($title_tmp, '#')) {$title_tmp = str_replace($new_tags, $new_values, $title_tmp);}
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
			$title .= "<a href=\"javascript: configure block\" onclick=\"window.open('index_edit.php?action=configure&amp;ctype={$ctype}&amp;block_id={$block_id}', '_blank', 'top=50,left=50,width=600,height=350,scrollbars=1,resizable=1'); return false;\">"
			."<img class=\"adminicon\" src=\"{$WT_IMAGES['admin']}\" width=\"15\" height=\"15\" border=\"0\" alt=\"".WT_I18N::translate('Configure').'" /></a>';
			$title .= help_link('index_htmlplus');
		}
		$title.=$title_tmp;

		$content = $html;

		if (get_block_setting($block_id, 'show_timestamp', false)) {
			$content.='<br/>'.format_timestamp(get_block_setting($block_id, 'timestamp', time()));
		}

		if ($template) {
			if (get_block_setting($block_id, 'block', false)) {
				require WT_THEME_DIR.'templates/block_small_temp.php';
			} else {
				require WT_THEME_DIR.'templates/block_main_temp.php';
			}
		} else {
			return $content;
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
			set_block_setting($block_id, 'gedcom',         safe_POST('gedcom'));
			set_block_setting($block_id, 'title',          $_POST['title']);
			set_block_setting($block_id, 'html',           $_POST['html']);
			set_block_setting($block_id, 'show_timestamp', safe_POST_bool('show_timestamp'));
			set_block_setting($block_id, 'timestamp',      safe_POST('timestamp'));
			$languages=array();
			foreach (WT_I18N::installed_languages() as $code=>$name) {
				if (safe_POST_bool('lang_'.$code)) {
					$languages[]=$code;
				}
			}
			set_block_setting($block_id, 'languages', implode(',', $languages));
			echo WT_JS_START, 'window.opener.location.href=window.opener.location.href;window.close();', WT_JS_END;
			exit;
		}

		require_once WT_ROOT.'includes/functions/functions_edit.php';

		$templates=array(
			WT_I18N::translate('Keyword examples')=>
			'#getAllTagsTable#',

			WT_I18N::translate('Narrative description')=>
			/* I18N: do not translate the #keywords# */ WT_I18N::translate('This GEDCOM (family tree) was last updated on #gedcomUpdated#. There are #totalSurnames# surnames in this family tree. The earliest recorded event is the #firstEventType# of #firstEventName# in #firstEventYear#. The most recent event is the #lastEventType# of #lastEventName# in #lastEventYear#.<br /><br />If you have any comments or feedback please contact #contactWebmaster#.'),

			WT_I18N::translate('GEDCOM statistics')=>
			'<div class="gedcom_stats">
				<span style="font-weight: bold"><a href="index.php?command=gedcom">#gedcomTitle#</a></span><br />
				'.WT_I18N::translate('This GEDCOM was created using <b>%1$s</b> on <b>%2$s</b>.', '#gedcomCreatedSoftware#', '#gedcomDate#').'
				<table id="keywords">
					<tr>
						<td valign="top" class="width20">
							<table cellspacing="1" cellpadding="0">
								<tr>
									<td class="facts_label">'.WT_I18N::translate('Individuals').'</td>
									<td class="facts_value">&nbsp;<a href="indilist.php?surname_sublist=no">#totalIndividuals#</a></td>
								</tr>
								<tr>
									<td class="facts_label">'.WT_I18N::translate('Total surnames').'</td>
									<td class="facts_value">&nbsp;<a href="indilist.php?surname_sublist=yes">#totalSurnames#</a></td>
								</tr>
								<tr>
									<td class="facts_label">'. WT_I18N::translate('Families').'</td>
									<td class="facts_value">&nbsp;<a href="famlist.php">#totalFamilies#</a></td>
								</tr>
								<tr>
									<td class="facts_label">'.WT_I18N::translate('Sources').'</td>
									<td class="facts_value">&nbsp;<a href="sourcelist.php">#totalSources#</a></td>
								</tr>
								<tr>
									<td class="facts_label">'.WT_I18N::translate('Media objects').'</td>
									<td class="facts_value">&nbsp;#totalMedia#</td>
								</tr>
								<tr>
									<td class="facts_label">'.WT_I18N::translate('Repositories').'</td>
									<td class="facts_value">&nbsp;#totalRepositories#</td>
								</tr>
								<tr>
									<td class="facts_label">'.WT_I18N::translate('Other records').'</td>
									<td class="facts_value">&nbsp;#totalOtherRecords#</td>
								</tr>
								<tr>
									<td class="facts_label">'.WT_I18N::translate('Total events').'</td>
									<td class="facts_value">&nbsp;#totalEvents#</td>
								</tr>
								<tr>
									<td class="facts_label">'.WT_I18N::translate('Males').'</td>
									<td class="facts_value">&nbsp;#totalSexMales# [#totalSexMalesPercentage#%]</td>
								</tr>
								<tr>
									<td class="facts_label">'.WT_I18N::translate('Females').'</td>
									<td class="facts_value">&nbsp;#totalSexFemales#  [#totalSexFemalesPercentage#%]</td>
								</tr>
								<tr>
									<td class="facts_label">'.WT_I18N::translate('Total users').'</td>
									<td class="facts_value">&nbsp;#totalUsers#</td>
								</tr>
							</table>
						</td>
						<td><br /></td>
						<td valign="top">
							<table cellspacing="1" cellpadding="0" border="0">
								<tr>
									<td class="facts_label">'.WT_I18N::translate('Earliest birth year').'</td>
									<td class="facts_value">&nbsp;#firstBirthYear#&nbsp;</td>
									<td class="facts_value">#firstBirth#</td>
								</tr>
								<tr>
									<td class="facts_label">'.WT_I18N::translate('Latest birth year').'</td>
									<td class="facts_value">&nbsp;#lastBirthYear#&nbsp;</td>
									<td class="facts_value">#lastBirth#</td>
								</tr>
								<tr>
									<td class="facts_label">'.WT_I18N::translate('Earliest death year').'</td>
									<td class="facts_value">&nbsp;#firstDeathYear#&nbsp;</td>
									<td class="facts_value">#firstDeath#</td>
								</tr>
								<tr>
									<td class="facts_label">'.WT_I18N::translate('Latest death year').'</td>
									<td class="facts_value">&nbsp;#lastDeathYear#&nbsp;</td>
									<td class="facts_value">#lastDeath#</td>
								</tr>
								<tr>
									<td class="facts_label">'.WT_I18N::translate('Person who lived the longest').'</td>
									<td class="facts_value">&nbsp;#longestLifeAge#&nbsp;</td>
									<td class="facts_value">#longestLife#</td>
								</tr>
								<tr>
									<td class="facts_label">'.WT_I18N::translate('Average age at death').'</td>
									<td class="facts_value">&nbsp;#averageLifespan#&nbsp;</td>
									<td class="facts_value"></td>
								</tr>
								<tr>
									<td class="facts_label">'.WT_I18N::translate('Family with the most children').'</td>
									<td class="facts_value">&nbsp;#largestFamilySize#&nbsp;</td>
									<td class="facts_value">#largestFamily#</td>
								</tr>
								<tr>
									<td class="facts_label">'.WT_I18N::translate('Average number of children per family').'</td>
									<td class="facts_value">&nbsp;#averageChildren#</td>
									<td class="facts_value"></td>
								</tr>
							</table>
						</td>
					</tr>
				</table><br />
				<span style="font-weight: bold">'.WT_I18N::translate('Most Common Surnames').'</span><br />
				#commonSurnames#
			</div>'
		);

		$title=get_block_setting($block_id, 'title');
		$html=get_block_setting($block_id, 'html');
		// title
		echo "<tr><td class=\"descriptionbox wrap width33\">"
			.translate_fact('TITL')
			.help_link('index_htmlplus_title')
			."</td><td class=\"optionbox\"><input type=\"text\" name=\"title\" size=\"30\" value=\"".htmlspecialchars($title)."\" /></td></tr>"
		;

		// templates
		echo "<tr><td class=\"descriptionbox wrap width33\">"
			.WT_I18N::translate('Templates')
			.help_link('index_htmlplus_template')
			."</td><td class=\"optionbox\">"
		;
		if (array_key_exists('ckeditor', WT_Module::getActiveModules()))
		{
			echo "<script language=\"JavaScript\" type=\"text/javascript\">\n"
				."t<!--\n"
				."function loadTemplate(html)"
				."{"
				."var oEditor = CKEDITOR.instances['html'];"
				."oEditor.setData(html);"
				."}"
				."-->\n"
				."</script>\n"
				."<select name=\"template\" onchange=\"loadTemplate(document.block.template.options[document.block.template.selectedIndex].value);\">"
			;
		}
		else
		{
			echo "<select name=\"template\" onchange=\"document.block.html.value=document.block.template.options[document.block.template.selectedIndex].value;\">";
		}
		echo "<option value=\"".htmlspecialchars($html)."\">".WT_I18N::translate('Custom')."</option>";
		foreach ($templates as $title=>$template)
		{
			echo "<option value=\"".htmlspecialchars($template)."\">{$title}</option>";
		}
		echo "</select>"
			."</td></tr>"
		;

		// gedcom
		$gedcoms = get_all_gedcoms();
		$gedcom=get_block_setting($block_id, 'gedcom');
		if (count($gedcoms) > 1)
		{
			if ($gedcom == '__current__') {$sel_current = ' selected="selected"';} else {$sel_current = '';}
			if ($gedcom == '__default__') {$sel_default = ' selected="selected"';} else {$sel_default = '';}
			echo "<tr><td class=\"descriptionbox wrap width33\">"
				.WT_I18N::translate('Family tree')
				.help_link('index_htmlplus_gedcom')
				."</td><td class=\"optionbox\">"
				."<select name=\"gedcom\">"
				."<option value=\"__current__\"{$sel_current}>".WT_I18N::translate('Current')."</option>"
				."<option value=\"__default__\"{$sel_default}>".WT_I18N::translate('Default')."</option>"
			;
			foreach ($gedcoms as $ged_id=>$ged_name)
			{
				if ($ged_name == $gedcom) {$sel = ' selected="selected"';} else {$sel = '';}
				echo "<option value=\"{$ged_name}\"{$sel}>".PrintReady(get_gedcom_setting($ged_id, 'title'))."</option>";
			}
			echo "</select>"
				."</td></tr>"
			;
		}

		// html
		echo "<tr><td class=\"descriptionbox wrap width33\">"
			.WT_I18N::translate('Content')
			.help_link('index_htmlplus_content')
			."<br /><br /></td>"
			."<td class=\"optionbox\">"
		;
		if (array_key_exists('ckeditor', WT_Module::getActiveModules())) {
			// use CKeditor module
			require_once WT_ROOT.'modules/ckeditor/ckeditor.php';
			$oCKeditor = new CKEditor();
			$oCKeditor->basePath =  './modules/ckeditor/';
			$oCKeditor->config['width'] = 700;
			$oCKeditor->config['height'] = 400;
			$oCKeditor->config['AutoDetectLanguage'] = false ;
			$oCKeditor->config['DefaultLanguage'] = 'en';
			$oCKeditor->editor('html', $html);
		}
		else
		{
			//use standard textarea
			echo "<textarea name=\"html\" rows=\"10\" cols=\"80\">".htmlspecialchars($html)."</textarea>";
		}

		echo "</td></tr>";

		$show_timestamp=get_block_setting($block_id, 'show_timestamp', false);
		echo '<tr><td class="descriptionbox wrap width33">';
		echo WT_I18N::translate('Show the date and time of update');
		echo '</td><td class="optionbox">';
		echo edit_field_yes_no('show_timestamp', $show_timestamp);
		echo '<input type="hidden" name="timestamp" value="'.time().'">';
		echo '</td></tr>';

		$languages=get_block_setting($block_id, 'languages');
		echo '<tr><td class="descriptionbox wrap width33">';
		echo WT_I18N::translate('Show this block for which languages?');
		echo '</td><td class="optionbox">';
		echo edit_language_checkboxes('lang_', $languages);
		echo '</td></tr>';
	}
}
