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

class gedcom_stats_WT_Module extends WT_Module implements WT_Module_Block {
	// Extend class WT_Module
	public function getTitle() {
		return i18n::translate('GEDCOM Statistics');
	}

	// Extend class WT_Module
	public function getDescription() {
		return i18n::translate('The GEDCOM Statistics block shows the visitor some basic information about the database, such as when it was created and how many people are in it.<br /><br />It also has a list of the most frequent surnames.  You can configure this block to not show the Frequent Surnames list, and you can also configure the GEDCOM to remove or add names to this list.  You can set the occurrence threshold for this list in the GEDCOM configuration.');
	}

	// Implement class WT_Module_Block
	public function getBlock($block_id) {
		global $ALLOW_CHANGE_GEDCOM, $ctype, $COMMON_NAMES_THRESHOLD, $WT_IMAGE_DIR, $WT_IMAGES, $MULTI_MEDIA, $top10_block_present, $THEME_DIR;

		$show_common_surnames=get_block_setting($block_id, 'show_common_surnames', true);
		$stat_indi           =get_block_setting($block_id, 'stat_indi',            true);
		$stat_fam            =get_block_setting($block_id, 'stat_fam',             true);
		$stat_sour           =get_block_setting($block_id, 'stat_sour',            true);
		$stat_other          =get_block_setting($block_id, 'stat_other',           true);
		$stat_media          =get_block_setting($block_id, 'stat_media',           true);
		$stat_surname        =get_block_setting($block_id, 'stat_surname',         true);
		$stat_events         =get_block_setting($block_id, 'stat_events',          true);
		$stat_users          =get_block_setting($block_id, 'stat_users',           true);
		$stat_first_birth    =get_block_setting($block_id, 'stat_first_birth',     true);
		$stat_last_birth     =get_block_setting($block_id, 'stat_last_birth',      true);
		$stat_first_death    =get_block_setting($block_id, 'stat_first_death',     true);
		$stat_last_death     =get_block_setting($block_id, 'stat_last_death',      true);
		$stat_long_life      =get_block_setting($block_id, 'stat_long_life',       true);
		$stat_avg_life       =get_block_setting($block_id, 'stat_avg_life',        true);
		$stat_most_chil      =get_block_setting($block_id, 'stat_most_chil',       true);
		$stat_avg_chil       =get_block_setting($block_id, 'stat_avg_chil',        true);
		$stat_link           =get_block_setting($block_id, 'stat_link',            true);

		$block=get_block_setting($block_id, 'block', false);

		$id=$this->getName().$block_id;
		$title='';
		if ($ctype=='gedcom' && WT_USER_GEDCOM_ADMIN || $ctype=='user' && WT_USER_ID) {
			$title .= "<a href=\"javascript: configure block\" onclick=\"window.open('".encode_url("index_edit.php?action=configure&block_id={$block_id}")."', '_blank', 'top=50,left=50,width=700,height=400,scrollbars=1,resizable=1'); return false;\">";
			$title .= "<img class=\"adminicon\" src=\"$WT_IMAGE_DIR/".$WT_IMAGES['admin']['small']."\" width=\"15\" height=\"15\" border=\"0\" alt=\"".i18n::translate('Configure')."\" /></a>";
		}
		$title.=i18n::translate('GEDCOM Statistics').help_link('index_stats');

		require_once WT_ROOT.'includes/classes/class_stats.php';
		$stats=new stats(WT_GEDCOM);

		$content = "<b><a href=\"index.php?ctype=gedcom\">".PrintReady(strip_tags(get_gedcom_setting(WT_GED_ID, 'title')))."</a></b><br />";
		$head = find_other_record('HEAD', WT_GED_ID);
		$ct=preg_match('/1 SOUR (.*)/', $head, $match);
		if ($ct>0) {
			$softrec = get_sub_record(1, '1 SOUR', $head);
			$tt= preg_match('/2 NAME (.*)/', $softrec, $tmatch);
			if ($tt>0) $software = printReady(trim($tmatch[1]));
			else $software = trim($match[1]);
			if (!empty($software)) {
				$tt = preg_match('/2 VERS (.*)/', $softrec, $tmatch);
				if ($tt>0) $version = printReady(trim($tmatch[1]));
				else $version='';
				$content .= i18n::translate('This GEDCOM was created using <b>%s %s</b>', $software, $version);
			}
		}
		if (preg_match('/1 DATE (.+)/', $head, $match)) {
			if (empty($software)) {
				$content.=i18n::translate('This GEDCOM was created on <b>%s</b>', $stats->gedcomDate());
			} else {
				$content.=i18n::translate(' on <b>%s</b>', $stats->gedcomDate());
			}
		}

		// I18N: %1$s = software program, %2$s = date
		$content .= i18n::translate('This GEDCOM was created using <b>%1$s</b> on <b>%2$s</b>.', $software, $version);
		if ($stat_indi) {
			$content.='<tr><td class="facts_label">'.i18n::translate('Individuals').'</td><td class="facts_value"><div dir="rtl"><a href="'.encode_url("indilist.php?surname_sublist=no&ged=".WT_GEDCOM).'">'.$stats->totalIndividuals().'</a></div></td></tr>';
			$content.='<tr><td class="facts_label">'.i18n::translate('Males').'</td><td class="facts_value"><div dir="rtl">'.$stats->totalSexMales().'<br />'.$stats->totalSexMalesPercentage().'%</div></td></tr>';
			$content.='<tr><td class="facts_label">'.i18n::translate('Females').'</td><td class="facts_value"><div dir="rtl">'.$stats->totalSexFemales().'<br />'.$stats->totalSexFemalesPercentage().'%</div></td></tr>';
		}
		if ($stat_surname) {
			$content .= '<tr><td class="facts_label">'.i18n::translate('Total surnames').'</td><td class="facts_value"><div dir="rtl"><a href="'.encode_url("indilist.php?show_all=yes&surname_sublist=yes&ged=".WT_GEDCOM).'">'.$stats->totalSurnames().'</a></div></td></tr>';
		}
		if ($stat_fam) {
			$content .= '<tr><td class="facts_label">'.i18n::translate('Families').'</td><td class="facts_value"><div dir="rtl"><a href="famlist.php">'.$stats->totalFamilies().'</a></div></td></tr>';
		}
		if ($stat_sour) {
			$content .= '<tr><td class="facts_label">'.i18n::translate('Sources').'</td><td class="facts_value"><div dir="rtl"><a href="sourcelist.php">'.$stats->totalSources().'</a></div></td></tr>';
		}
		if ($stat_media && $MULTI_MEDIA==true) {
			$content .= '<tr><td class="facts_label">'.i18n::translate('Media objects').'</td><td class="facts_value"><div dir="rtl"><a href="medialist.php">'.$stats->totalMedia().'</a></div></td></tr>';
		}
		if ($stat_other) {
			$content .= '<tr><td class="facts_label">'.i18n::translate('Other records').'</td><td class="facts_value"><div dir="rtl">'.$stats->totalOtherRecords().'</div></td></tr>';
		}
		if ($stat_events) {
			$content .= '<tr><td class="facts_label">'.i18n::translate('Total events').'</td><td class="facts_value"><div dir="rtl">'.$stats->totalEvents().'</div></td></tr>';
		}
		if ($stat_users) {
			$content .= '<tr><td class="facts_label">'.i18n::translate('Total users').'</td><td class="facts_value"><div dir="rtl">';
				if (WT_USER_GEDCOM_ADMIN){
				$content .= '<a href="useradmin.php">'.$stats->totalUsers().'</a>';
			} else {
				$content .= $stats->totalUsers();
			}
			$content .= '</div>
	</td>
	</tr>';
		}
		if (!$block) {
			$content .= '</table></td><td><br /></td><td valign="top"><table cellspacing="1" cellpadding="1" border="0">';
		}
		if ($stat_first_birth) {
			$content .= '<tr><td class="facts_label">'.i18n::translate('Earliest birth year').'</td><td class="facts_value"><div dir="rtl">'.$stats->firstBirthYear().'</div></td>';
			if (!$block) {
				$content .= '<td class="facts_value">'.$stats->firstBirth().'</td>';
			}
			$content .= '</tr>';
		}
		if ($stat_last_birth) {
			$content .= '<tr><td class="facts_label">'.i18n::translate('Latest birth year').'</td><td class="facts_value"><div dir="rtl">'.$stats->lastBirthYear().'</div></td>';
			if (!$block){
				$content .= '<td class="facts_value">'.$stats->lastBirth().'</td>';
			}
			$content .= '</tr>';
		}
		if ($stat_first_death) {
			$content .= '<tr><td class="facts_label">'.i18n::translate('Earliest death year').'</td><td class="facts_value"><div dir="rtl">'.$stats->firstDeathYear().'</div></td>';
			if (!$block){
				$content .= '<td class="facts_value">'.$stats->firstDeath().'</td>';
			}
			$content .= '</tr>';
		}
		if ($stat_last_death) {
			$content .= '<tr><td class="facts_label">'.i18n::translate('Latest death year').'</td><td class="facts_value"><div dir="rtl">'.$stats->lastDeathYear().'</div>
	</td>';
			if (!$block){
				$content .= '<td class="facts_value">'.$stats->lastDeath().'</td>';
			}
			$content .='</tr>';
		}
		if ($stat_long_life) {
			$content .= '<tr><td class="facts_label">'.i18n::translate('Person who lived the longest').'</td><td class="facts_value"><div dir="rtl">'.$stats->LongestLifeAge().'</div></td>';
			if (!$block){
				$content .= '<td class="facts_value">'.$stats->LongestLife().'</td>';
			}
			$content .= '</tr>';
		}
		if ($stat_avg_life) {
			$content .= '<tr><td class="facts_label">'.i18n::translate('Average age at death').'</td><td class="facts_value"><div dir="rtl">'.$stats->averageLifespan().'</div></td>';
			if (!$block) {
				$content .= '<td class="facts_value">'.i18n::translate('Males').':&nbsp;'.$stats->averageLifespanMale();
				$content .= '&nbsp;&nbsp;&nbsp;'.i18n::translate('Females').':&nbsp;'.$stats->averageLifespanFemale().'</td>';
			}
			$content .= '</tr>';
		}

		if ($stat_most_chil && !$block) {
			$content .= '<tr><td class="facts_label">'.i18n::translate('Family with the most children').'</td><td class="facts_value"><div dir="rtl">'.$stats->largestFamilySize().'</div></td>';
			if (!$block) {
				$content .= '<td class="facts_value">'.$stats->largestFamily().'</td>';
			}
			$content .= '</tr>';
		}
		if ($stat_avg_chil) {
			$content .= '<tr><td class="facts_label">'.i18n::translate('Average number of children per family').'</td><td class="facts_value"><div dir="rtl">'.$stats->averageChildren().'</div></td>';
			if (!$block) {
				$content .= '<td class="facts_value">&nbsp;</td>';
			}
			$content .= '</tr>';
		}
		$content .= '</table></td></tr></table>';
		if ($stat_link) {
			$content .= '<a href="statistics.php"><b>'.i18n::translate('View statistics as graphs').'</b></a><br />';
		}
		// NOTE: Print the most common surnames
		if ($show_common_surnames) {
			$surnames = get_common_surnames($COMMON_NAMES_THRESHOLD);
			if (count($surnames)>0) {
				$content .= '<br /><b>'.i18n::translate('Most Common Surnames').'</b>';
				$content .= help_link('index_common_names');
				$content .= '<br />';
				$i=0;
				foreach($surnames as $indexval => $surname) {
					if (stristr($surname['name'], '@N.N')===false) {
						if ($i>0) {
							$content .= ', ';
						}
						$content .= '<a href="'.encode_url("indilist.php?ged=".WT_GEDCOM."&surname=".$surname['name']).'">'.PrintReady($surname['name']).'</a>';
						$i++;
					}
				}
			}
		}

		require $THEME_DIR.'templates/block_main_temp.php';
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
			set_block_setting($block_id, 'show_common_surnames', safe_POST_bool('show_common_surnames'));
			set_block_setting($block_id, 'stat_indi',            safe_POST_bool('stat_indi'));
			set_block_setting($block_id, 'stat_fam',             safe_POST_bool('stat_fam'));
			set_block_setting($block_id, 'stat_sour',            safe_POST_bool('stat_sour'));
			set_block_setting($block_id, 'stat_other',           safe_POST_bool('stat_other'));
			set_block_setting($block_id, 'stat_media',           safe_POST_bool('stat_media'));
			set_block_setting($block_id, 'stat_surname',         safe_POST_bool('stat_surname'));
			set_block_setting($block_id, 'stat_events',          safe_POST_bool('stat_events'));
			set_block_setting($block_id, 'stat_users',           safe_POST_bool('stat_users'));
			set_block_setting($block_id, 'stat_first_birth',     safe_POST_bool('stat_first_birth'));
			set_block_setting($block_id, 'stat_last_birth',      safe_POST_bool('stat_last_birth'));
			set_block_setting($block_id, 'stat_first_death',     safe_POST_bool('stat_first_death'));
			set_block_setting($block_id, 'stat_last_death',      safe_POST_bool('stat_last_death'));
			set_block_setting($block_id, 'stat_long_life',       safe_POST_bool('stat_long_life'));
			set_block_setting($block_id, 'stat_avg_life',        safe_POST_bool('stat_avg_life'));
			set_block_setting($block_id, 'stat_most_chil',       safe_POST_bool('stat_most_chil'));
			set_block_setting($block_id, 'stat_avg_chil',        safe_POST_bool('stat_avg_chil'));
			set_block_setting($block_id, 'stat_link',            safe_POST_bool('stat_link'));
			echo WT_JS_START, 'window.opener.location.href=window.opener.location.href;window.close();', WT_JS_END;
			exit;
		}

		require_once WT_ROOT.'includes/functions/functions_edit.php';

		$show_common_surnames=get_block_setting($block_id, 'show_common_surnames', true);
		echo '<tr><td class="descriptionbox wrap width33">';
		echo i18n::translate('Show common surnames?'), help_link('show_common_surnames');
		echo '</td><td class="optionbox">';
		echo edit_field_yes_no('show_common_surnames', $show_common_surnames);
		echo '</td></tr>';

		$stat_indi           =get_block_setting($block_id, 'stat_indi',            true);
		$stat_fam            =get_block_setting($block_id, 'stat_fam',             true);
		$stat_sour           =get_block_setting($block_id, 'stat_sour',            true);
		$stat_other          =get_block_setting($block_id, 'stat_other',           true);
		$stat_media          =get_block_setting($block_id, 'stat_media',           true);
		$stat_surname        =get_block_setting($block_id, 'stat_surname',         true);
		$stat_events         =get_block_setting($block_id, 'stat_events',          true);
		$stat_users          =get_block_setting($block_id, 'stat_users',           true);
		$stat_first_birth    =get_block_setting($block_id, 'stat_first_birth',     true);
		$stat_last_birth     =get_block_setting($block_id, 'stat_last_birth',      true);
		$stat_first_death    =get_block_setting($block_id, 'stat_first_death',     true);
		$stat_last_death     =get_block_setting($block_id, 'stat_last_death',      true);
		$stat_long_life      =get_block_setting($block_id, 'stat_long_life',       true);
		$stat_avg_life       =get_block_setting($block_id, 'stat_avg_life',        true);
		$stat_most_chil      =get_block_setting($block_id, 'stat_most_chil',       true);
		$stat_avg_chil       =get_block_setting($block_id, 'stat_avg_chil',        true);
		$stat_link           =get_block_setting($block_id, 'stat_link',            true);
?>
	<tr>
	<td class="descriptionbox wrap width33"><?php echo i18n::translate('Select the stats to show in this block'); ?></td>
	<td class="optionbox">
	<table>
		<tr>
			<td><input type="checkbox" value="yes" name="stat_indi"
			<?php if ($stat_indi) echo ' checked="checked"'; ?> />
			<?php echo i18n::translate('Individuals'); ?></td>
			<td><input type="checkbox" value="yes" name="stat_first_birth"
			<?php if ($stat_first_birth) echo ' checked="checked"'; ?> />
			<?php echo i18n::translate('Earliest birth year'); ?></td>
		</tr>
		<tr>
			<td><input type="checkbox" value="yes" name="stat_surname"
			<?php if ($stat_surname) echo ' checked="checked"'; ?> />
			<?php echo i18n::translate('Total surnames'); ?></td>
			<td><input type="checkbox" value="yes" name="stat_last_birth"
			<?php if ($stat_last_birth) echo ' checked="checked"'; ?> />
			<?php echo i18n::translate('Latest birth year'); ?></td>
		</tr>
		<tr>
			<td><input type="checkbox" value="yes" name="stat_fam"
			<?php if ($stat_fam) echo ' checked="checked"'; ?> />
			<?php echo i18n::translate('Families'); ?></td>
			<td><input type="checkbox" value="yes" name="stat_first_death"
			<?php if ($stat_first_death) echo ' checked="checked"'; ?> />
			<?php echo i18n::translate('Earliest death year'); ?></td>
		</tr>
		<tr>
			<td><input type="checkbox" value="yes" name="stat_sour"
			<?php if ($stat_sour) echo ' checked="checked"'; ?> />
			<?php echo i18n::translate('Sources'); ?></td>
			<td><input type="checkbox" value="yes" name="stat_last_death"
			<?php if ($stat_last_death) echo ' checked="checked"'; ?> />
			<?php echo i18n::translate('Latest death year'); ?></td>
		</tr>
		<tr>
			<td><input type="checkbox" value="yes" name="stat_media"
			<?php if ($stat_media) echo ' checked="checked"'; ?> />
			<?php echo i18n::translate('Media objects'); ?></td>
			<td><input type="checkbox" value="yes" name="stat_long_life"
			<?php if ($stat_long_life) echo ' checked="checked"'; ?> />
			<?php echo i18n::translate('Person who lived the longest'); ?></td>
		</tr>
		<tr>
			<td><input type="checkbox" value="yes" name="stat_other"
			<?php if ($stat_other) echo ' checked="checked"'; ?> />
			<?php echo i18n::translate('Other records'); ?></td>
			<td><input type="checkbox" value="yes" name="stat_avg_life"
			<?php if ($stat_avg_life) echo ' checked="checked"'; ?> />
			<?php echo i18n::translate('Average age at death'); ?></td>
		</tr>
		<tr>
			<td><input type="checkbox" value="yes" name="stat_events"
			<?php if ($stat_events) echo ' checked="checked"'; ?> />
			<?php echo i18n::translate('Total events'); ?></td>
			<td><input type="checkbox" value="yes" name="stat_most_chil"
			<?php if ($stat_most_chil) echo ' checked="checked"'; ?> />
			<?php echo i18n::translate('Family with the most children'); ?></td>
		</tr>
		<tr>
			<td><input type="checkbox" value="yes" name="stat_users"
			<?php if ($stat_users) echo ' checked="checked"'; ?> />
			<?php echo i18n::translate('Total users'); ?></td>
			<td><input type="checkbox" value="yes" name="stat_avg_chil"
			<?php if ($stat_avg_chil) echo ' checked="checked"'; ?> />
			<?php echo i18n::translate('Average number of children per family'); ?></td>
		</tr>
	</table>
	</td>
	</tr>
	<?php
		$stat_link=get_block_setting($block_id, 'stat_link', true);
		echo '<tr><td class="descriptionbox wrap width33">';
		echo i18n::translate('Show link to Statistics charts?'), help_link('show_common_surnames');
		echo '</td><td class="optionbox">';
		echo edit_field_yes_no('stat_link', $stat_link);
		echo '</td></tr>';
	}
}
