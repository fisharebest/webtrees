<?php
// Classes and libraries for module system
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
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
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

use WT\Auth;

class html_WT_Module extends WT_Module implements WT_Module_Block {
	// Extend class WT_Module
	public function getTitle() {
		return /* I18N: Name of a module */ WT_I18N::translate('HTML');
	}

	// Extend class WT_Module
	public function getDescription() {
		return /* I18N: Description of the “HTML” module */ WT_I18N::translate('Add your own text and graphics.');
	}

	// Implement class WT_Module_Block
	public function getBlock($block_id, $template=true, $cfg=null) {
		global $ctype, $GEDCOM;

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
			$GEDCOM=WT_Site::getPreference('DEFAULT_GEDCOM');
			if (!$GEDCOM) {
				foreach (WT_Tree::getAll() as $tree) {
					$GEDCOM=$tree->tree_name;
					break;
				}
			}
			break;
		default:
			$GEDCOM = $gedcom;
			break;
		}

		/*
		* Retrieve text, process embedded variables
		*/
		$title_tmp = get_block_setting($block_id, 'title');
		$html = get_block_setting($block_id, 'html');

		if ( (strpos($title_tmp, '#')!==false) || (strpos($html, '#')!==false) ) {
			$stats = new WT_Stats($GEDCOM);
			$title_tmp = $stats->embedTags($title_tmp);
			$html = $stats->embedTags($html);
		}

		/*
		* Restore Current GEDCOM
		*/
		$GEDCOM = WT_GEDCOM;

		/*
		* Start Of Output
		*/
		$id=$this->getName().$block_id;
		$class=$this->getName().'_block';
		if ($ctype === 'gedcom' && WT_USER_GEDCOM_ADMIN || $ctype === 'user' && Auth::check()) {
			$title='<i class="icon-admin" title="'.WT_I18N::translate('Configure').'" onclick="modalDialog(\'block_edit.php?block_id='.$block_id.'\', \''.$this->getTitle().'\');"></i>';
		} else {
			$title='';
		}
		$title.=$title_tmp;

		$content = $html;

		if (get_block_setting($block_id, 'show_timestamp', false)) {
			$content.='<br>'.format_timestamp(get_block_setting($block_id, 'timestamp', WT_TIMESTAMP));
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
		if (WT_Filter::postBool('save') && WT_Filter::checkCsrf()) {
			set_block_setting($block_id, 'gedcom',         WT_Filter::post('gedcom'));
			set_block_setting($block_id, 'title',          WT_Filter::post('title'));
			set_block_setting($block_id, 'html',           WT_Filter::post('html'));
			set_block_setting($block_id, 'show_timestamp', WT_Filter::postBool('show_timestamp'));
			set_block_setting($block_id, 'timestamp',      WT_Filter::post('timestamp'));
			$languages=array();
			foreach (WT_I18N::installed_languages() as $code=>$name) {
				if (WT_Filter::postBool('lang_'.$code)) {
					$languages[]=$code;
				}
			}
			set_block_setting($block_id, 'languages', implode(',', $languages));
			exit;
		}

		require_once WT_ROOT.'includes/functions/functions_edit.php';

		$templates=array(
			WT_I18N::translate('Keyword examples')=>
			'#getAllTagsTable#',

			WT_I18N::translate('Narrative description')=>
			/* I18N: do not translate the #keywords# */ WT_I18N::translate('This family tree was last updated on #gedcomUpdated#. There are #totalSurnames# surnames in this family tree.  The earliest recorded event is the #firstEventType# of #firstEventName# in #firstEventYear#. The most recent event is the #lastEventType# of #lastEventName# in #lastEventYear#.<br><br>If you have any comments or feedback please contact #contactWebmaster#.'),

			WT_I18N::translate('Statistics')=>
			'<div class="gedcom_stats">
				<span style="font-weight: bold"><a href="index.php?command=gedcom">#gedcomTitle#</a></span><br>
				' . WT_I18N::translate('This family tree was last updated on %s.', '#gedcomUpdated#') . '
				<table id="keywords">
					<tr>
						<td valign="top" class="width20">
							<table cellspacing="1" cellpadding="0">
								<tr>
									<td class="facts_label">'.WT_I18N::translate('Individuals').'</td>
									<td class="facts_value" align="right"><a href="indilist.php?surname_sublist=no">#totalIndividuals#</a></td>
								</tr>
								<tr>
									<td class="facts_label">'.WT_I18N::translate('Males').'</td>
									<td class="facts_value" align="right">#totalSexMales#<br>#totalSexMalesPercentage#</td>
								</tr>
								<tr>
									<td class="facts_label">'.WT_I18N::translate('Females').'</td>
									<td class="facts_value" align="right">#totalSexFemales#<br>#totalSexFemalesPercentage#</td>
								</tr>
								<tr>
									<td class="facts_label">'.WT_I18N::translate('Total surnames').'</td>
									<td class="facts_value" align="right"><a href="indilist.php?show_all=yes&amp;surname_sublist=yes&amp;ged='.WT_GEDURL.'">#totalSurnames#</a></td>
								</tr>
								<tr>
									<td class="facts_label">'. WT_I18N::translate('Families').'</td>
									<td class="facts_value" align="right"><a href="famlist.php?ged='.WT_GEDURL.'">#totalFamilies#</a></td>
								</tr>
								<tr>
									<td class="facts_label">'.WT_I18N::translate('Sources').'</td>
									<td class="facts_value" align="right"><a href="sourcelist.php?ged='.WT_GEDURL.'">#totalSources#</a></td>
								</tr>
								<tr>
									<td class="facts_label">'.WT_I18N::translate('Media objects').'</td>
									<td class="facts_value" align="right"><a href="medialist.php?ged='.WT_GEDURL.'">#totalMedia#</a></td>
								</tr>
								<tr>
									<td class="facts_label">'.WT_I18N::translate('Repositories').'</td>
									<td class="facts_value" align="right"><a href="repolist.php?ged='.WT_GEDURL.'">#totalRepositories#</a></td>
								</tr>
								<tr>
									<td class="facts_label">'.WT_I18N::translate('Total events').'</td>
									<td class="facts_value" align="right">#totalEvents#</td>
								</tr>
								<tr>
									<td class="facts_label">'.WT_I18N::translate('Total users').'</td>
									<td class="facts_value" align="right">#totalUsers#</td>
								</tr>
							</table>
						</td>
						<td><br></td>
						<td valign="top">
							<table cellspacing="1" cellpadding="0" border="0">
								<tr>
									<td class="facts_label">'.WT_I18N::translate('Earliest birth year').'</td>
									<td class="facts_value" align="right">#firstBirthYear#</td>
									<td class="facts_value">#firstBirth#</td>
								</tr>
								<tr>
									<td class="facts_label">'.WT_I18N::translate('Latest birth year').'</td>
									<td class="facts_value" align="right">#lastBirthYear#</td>
									<td class="facts_value">#lastBirth#</td>
								</tr>
								<tr>
									<td class="facts_label">'.WT_I18N::translate('Earliest death year').'</td>
									<td class="facts_value" align="right">#firstDeathYear#</td>
									<td class="facts_value">#firstDeath#</td>
								</tr>
								<tr>
									<td class="facts_label">'.WT_I18N::translate('Latest death year').'</td>
									<td class="facts_value" align="right">#lastDeathYear#</td>
									<td class="facts_value">#lastDeath#</td>
								</tr>
								<tr>
									<td class="facts_label">'.WT_I18N::translate('Individual who lived the longest').'</td>
									<td class="facts_value" align="right">#longestLifeAge#</td>
									<td class="facts_value">#longestLife#</td>
								</tr>
								<tr>
									<td class="facts_label">'.WT_I18N::translate('Average age at death').'</td>
									<td class="facts_value" align="right">#averageLifespan#</td>
									<td class="facts_value"></td>
								</tr>
								<tr>
									<td class="facts_label">'.WT_I18N::translate('Family with the most children').'</td>
									<td class="facts_value" align="right">#largestFamilySize#</td>
									<td class="facts_value">#largestFamily#</td>
								</tr>
								<tr>
									<td class="facts_label">'.WT_I18N::translate('Average number of children per family').'</td>
									<td class="facts_value" align="right">#averageChildren#</td>
									<td class="facts_value"></td>
								</tr>
							</table>
						</td>
					</tr>
				</table><br>
				<span style="font-weight: bold">'.WT_I18N::translate('Most common surnames').'</span><br>
				#commonSurnames#
			</div>'
		);

		$title=get_block_setting($block_id, 'title');
		$html=get_block_setting($block_id, 'html');
		// title
		echo '<tr><td class="descriptionbox wrap">',
			WT_Gedcom_Tag::getLabel('TITL'),
			'</td><td class="optionbox"><input type="text" name="title" size="30" value="', WT_Filter::escapeHtml($title), '"></td></tr>';

		// templates
		echo '<tr><td class="descriptionbox wrap">',
			WT_I18N::translate('Templates'),
			help_link('block_html_template', $this->getName()),
			'</td><td class="optionbox">';
		// The CK editor needs lots of help to load/save data :-(
		if (array_key_exists('ckeditor', WT_Module::getActiveModules())) {
			$ckeditor_onchange='CKEDITOR.instances.html.setData(document.block.html.value);';
		} else {
			$ckeditor_onchange='';
		}
		echo '<select name="template" onchange="document.block.html.value=document.block.template.options[document.block.template.selectedIndex].value;', $ckeditor_onchange, '">';
		echo '<option value="', WT_Filter::escapeHtml($html), '">', WT_I18N::translate('Custom'), '</option>';
		foreach ($templates as $title=>$template) {
			echo '<option value="', WT_Filter::escapeHtml($template), '">', $title, '</option>';
		}
		echo '</select></td></tr>';

		// gedcom
		$gedcom=get_block_setting($block_id, 'gedcom');
		if (count(WT_Tree::getAll()) > 1) {
			if ($gedcom == '__current__') {$sel_current = ' selected="selected"';} else {$sel_current = '';}
			if ($gedcom == '__default__') {$sel_default = ' selected="selected"';} else {$sel_default = '';}
			echo '<tr><td class="descriptionbox wrap">',
				WT_I18N::translate('Family tree'),
				'</td><td class="optionbox">',
				'<select name="gedcom">',
				'<option value="__current__"', $sel_current, '>', WT_I18N::translate('Current'), '</option>',
				'<option value="__default__"', $sel_default, '>', WT_I18N::translate('Default'), '</option>';
			foreach (WT_Tree::getAll() as $tree) {
				if ($tree->tree_name == $gedcom) {$sel = ' selected="selected"';} else {$sel = '';}
				echo '<option value="', $tree->tree_name, '"', $sel, ' dir="auto">', $tree->tree_title_html, '</option>';
			}
			echo '</select></td></tr>';
		}

		// html
		echo '<tr><td colspan="2" class="descriptionbox">',
			WT_I18N::translate('Content'),
			help_link('block_html_content', $this->getName()),
			'</td></tr><tr>',
			'<td colspan="2" class="optionbox">';
		echo '<textarea name="html" class="html-edit" rows="10" style="width:98%;">', WT_Filter::escapeHtml($html), '</textarea>';
		echo '</td></tr>';

		$show_timestamp=get_block_setting($block_id, 'show_timestamp', false);
		echo '<tr><td class="descriptionbox wrap">';
		echo WT_I18N::translate('Show the date and time of update');
		echo '</td><td class="optionbox">';
		echo edit_field_yes_no('show_timestamp', $show_timestamp);
		echo '<input type="hidden" name="timestamp" value="', WT_TIMESTAMP, '">';
		echo '</td></tr>';

		$languages=get_block_setting($block_id, 'languages');
		echo '<tr><td class="descriptionbox wrap">';
		echo WT_I18N::translate('Show this block for which languages?');
		echo '</td><td class="optionbox">';
		echo edit_language_checkboxes('lang_', $languages);
		echo '</td></tr>';
	}
}
