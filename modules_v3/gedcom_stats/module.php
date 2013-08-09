<?php
// Classes and libraries for module system
//
// webtrees: Web based Family History software
// Copyright (C) 2013 webtrees development team.
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

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class gedcom_stats_WT_Module extends WT_Module implements WT_Module_Block {
	// Extend class WT_Module
	public function getTitle() {
		return /* I18N: Name of a module */ WT_I18N::translate('Statistics');
	}

	// Extend class WT_Module
	public function getDescription() {
		return /* I18N: Description of “Statistics” module */ WT_I18N::translate('The size of the family tree, earliest and latest events, common names, etc.');
	}

	// Implement class WT_Module_Block
	public function getBlock($block_id, $template=true, $cfg=null) {
		global $ctype, $top10_block_present;

		$show_last_update    =get_block_setting($block_id, 'show_last_update',     true);
		$show_common_surnames=get_block_setting($block_id, 'show_common_surnames', true);
		$stat_indi           =get_block_setting($block_id, 'stat_indi',            true);
		$stat_fam            =get_block_setting($block_id, 'stat_fam',             true);
		$stat_sour           =get_block_setting($block_id, 'stat_sour',            true);
		$stat_media          =get_block_setting($block_id, 'stat_media',           true);
		$stat_repo           =get_block_setting($block_id, 'stat_repo',            true);
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
		$block               =get_block_setting($block_id, 'block',                false);
		if ($cfg) {
			foreach (array('show_common_surnames', 'stat_indi', 'stat_fam', 'stat_sour', 'stat_media', 'stat_surname', 'stat_events', 'stat_users', 'stat_first_birth', 'stat_last_birth', 'stat_first_death', 'stat_last_death', 'stat_long_life', 'stat_avg_life', 'stat_most_chil', 'stat_avg_chil', 'stat_link', 'block') as $name) {
				if (array_key_exists($name, $cfg)) {
					$$name=$cfg[$name];
				}
			}
		}

		$id=$this->getName().$block_id;
		$class=$this->getName().'_block';
		if ($ctype=='gedcom' && WT_USER_GEDCOM_ADMIN || $ctype=='user' && WT_USER_ID) {
			$title='<i class="icon-admin" title="'.WT_I18N::translate('Configure').'" onclick="modalDialog(\'block_edit.php?block_id='.$block_id.'\', \''.$this->getTitle().'\');"></i>';
		} else {
			$title='';
		}
		$title.=$this->getTitle();

		$stats=new WT_Stats(WT_GEDCOM);

		$content = '<b>'.WT_TREE_TITLE.'</b><br>';

		if ($show_last_update) {
			$content .= '<div>'./* I18N: %s is a date */ WT_I18N::translate('This family tree was last updated on %s.', strip_tags($stats->gedcomUpdated())).'</div>';
		}

		$content .= '<table><tr><td class="width20"><table class="facts_table">';
		if ($stat_indi) {
			$content.='<tr><td class="facts_label">'.WT_I18N::translate('Individuals').'</td><td class="facts_value stats_value"><a href="'."indilist.php?surname_sublist=no&amp;ged=".WT_GEDURL.'">'.$stats->totalIndividuals().'</a></td></tr>';
			$content.='<tr><td class="facts_label">'.WT_I18N::translate('Males').'</td><td class="facts_value stats_value">'.$stats->totalSexMales().'<br>'.$stats->totalSexMalesPercentage().'</td></tr>';
			$content.='<tr><td class="facts_label">'.WT_I18N::translate('Females').'</td><td class="facts_value stats_value">'.$stats->totalSexFemales().'<br>'.$stats->totalSexFemalesPercentage().'</td></tr>';
		}
		if ($stat_surname) {
			$content .= '<tr><td class="facts_label">'.WT_I18N::translate('Total surnames').'</td><td class="facts_value stats_value"><a href="indilist.php?show_all=yes&amp;surname_sublist=yes&amp;ged='.WT_GEDURL.'">'.$stats->totalSurnames().'</a></td></tr>';
		}
		if ($stat_fam) {
			$content .= '<tr><td class="facts_label">'.WT_I18N::translate('Families').'</td><td class="facts_value stats_value"><a href="famlist.php?ged='.WT_GEDURL.'">'.$stats->totalFamilies().'</a></td></tr>';
		}
		if ($stat_sour) {
			$content .= '<tr><td class="facts_label">'.WT_I18N::translate('Sources').'</td><td class="facts_value stats_value"><a href="sourcelist.php?ged='.WT_GEDURL.'">'.$stats->totalSources().'</a></td></tr>';
		}
		if ($stat_media) {
			$content .= '<tr><td class="facts_label">'.WT_I18N::translate('Media objects').'</td><td class="facts_value stats_value"><a href="medialist.php?ged='.WT_GEDURL.'">'.$stats->totalMedia().'</a></td></tr>';
		}
		if ($stat_repo) {
			$content .= '<tr><td class="facts_label">'.WT_I18N::translate('Repositories').'</td><td class="facts_value stats_value"><a href="repolist.php?ged='.WT_GEDURL.'">'.$stats->totalRepositories().'</a></td></tr>';
		}
		if ($stat_events) {
			$content .= '<tr><td class="facts_label">'.WT_I18N::translate('Total events').'</td><td class="facts_value stats_value">'.$stats->totalEvents().'</td></tr>';
		}
		if ($stat_users) {
			$content .= '<tr><td class="facts_label">'.WT_I18N::translate('Total users').'</td><td class="facts_value stats_value">';
			if (WT_USER_GEDCOM_ADMIN) {
				$content .= '<a href="admin_users.php">'.$stats->totalUsers().'</a>';
			} else {
				$content .= $stats->totalUsers();
			}
			$content .= '</td></tr>';
		}
		if (!$block) {
			$content .= '</table></td><td><table class="facts_table">';
		}
		if ($stat_first_birth) {
			$content .= '<tr><td class="facts_label">'.WT_I18N::translate('Earliest birth year').'</td><td class="facts_value stats_value">'.$stats->firstBirthYear().'</td>';
			if (!$block) {
				$content .= '<td class="facts_value">'.$stats->firstBirth().'</td>';
			}
			$content .= '</tr>';
		}
		if ($stat_last_birth) {
			$content .= '<tr><td class="facts_label">'.WT_I18N::translate('Latest birth year').'</td><td class="facts_value stats_value">'.$stats->lastBirthYear().'</td>';
			if (!$block) {
				$content .= '<td class="facts_value">'.$stats->lastBirth().'</td>';
			}
			$content .= '</tr>';
		}
		if ($stat_first_death) {
			$content .= '<tr><td class="facts_label">'.WT_I18N::translate('Earliest death year').'</td><td class="facts_value stats_value">'.$stats->firstDeathYear().'</td>';
			if (!$block) {
				$content .= '<td class="facts_value">'.$stats->firstDeath().'</td>';
			}
			$content .= '</tr>';
		}
		if ($stat_last_death) {
			$content .= '<tr><td class="facts_label">'.WT_I18N::translate('Latest death year').'</td><td class="facts_value stats_value">'.$stats->lastDeathYear().'
	</td>';
			if (!$block) {
				$content .= '<td class="facts_value">'.$stats->lastDeath().'</td>';
			}
			$content .='</tr>';
		}
		if ($stat_long_life) {
			$content .= '<tr><td class="facts_label">'.WT_I18N::translate('Person who lived the longest').'</td><td class="facts_value stats_value">'.$stats->LongestLifeAge().'</td>';
			if (!$block) {
				$content .= '<td class="facts_value">'.$stats->LongestLife().'</td>';
			}
			$content .= '</tr>';
		}
		if ($stat_avg_life) {
			$content .= '<tr><td class="facts_label">'.WT_I18N::translate('Average age at death').'</td><td class="facts_value stats_value">'.$stats->averageLifespan().'</td>';
			if (!$block) {
				$content .= '<td class="facts_value">'.WT_I18N::translate('Males').':&nbsp;'.$stats->averageLifespanMale();
				$content .= '&nbsp;&nbsp;&nbsp;'.WT_I18N::translate('Females').':&nbsp;'.$stats->averageLifespanFemale().'</td>';
			}
			$content .= '</tr>';
		}

		if ($stat_most_chil && !$block) {
			$content .= '<tr><td class="facts_label">'.WT_I18N::translate('Family with the most children').'</td><td class="facts_value stats_value">'.$stats->largestFamilySize().'</td>';
			if (!$block) {
				$content .= '<td class="facts_value">'.$stats->largestFamily().'</td>';
			}
			$content .= '</tr>';
		}
		if ($stat_avg_chil) {
			$content .= '<tr><td class="facts_label">'.WT_I18N::translate('Average number of children per family').'</td><td class="facts_value stats_value">'.$stats->averageChildren().'</td>';
			if (!$block) {
				$content .= '<td class="facts_value">&nbsp;</td>';
			}
			$content .= '</tr>';
		}
		$content .= '</table></td></tr></table>';
		if ($stat_link) {
			$content .= '<a href="statistics.php?ged='.WT_GEDURL.'"><b>'.WT_I18N::translate('View statistics as graphs').'</b></a><br>';
		}
		// NOTE: Print the most common surnames
		if ($show_common_surnames) {
			$surnames = get_common_surnames(get_gedcom_setting(WT_GED_ID, 'COMMON_NAMES_THRESHOLD'));
			if (count($surnames)>0) {
				$content .= '<p><b>'.WT_I18N::translate('Most Common Surnames').'</b></p>';
				$content .= '<div class="common_surnames">';
				$i=0;
				foreach ($surnames as $indexval => $surname) {
					if (stristr($surname['name'], '@N.N')===false) {
						if ($i>0) {
							$content .= ', ';
						}
						$content .= '<a href="'."indilist.php?ged=".WT_GEDURL."&amp;surname=".rawurlencode($surname['name']).'">'.$surname['name'].'</a>';
						$i++;
					}
				}
				$content .= '</div>';
			}
		}

		if ($template) {
			require WT_THEME_DIR.'templates/block_main_temp.php';
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
			set_block_setting($block_id, 'show_last_update',     safe_POST_bool('show_last_update'));
			set_block_setting($block_id, 'show_common_surnames', safe_POST_bool('show_common_surnames'));
			set_block_setting($block_id, 'stat_indi',            safe_POST_bool('stat_indi'));
			set_block_setting($block_id, 'stat_fam',             safe_POST_bool('stat_fam'));
			set_block_setting($block_id, 'stat_sour',            safe_POST_bool('stat_sour'));
			set_block_setting($block_id, 'stat_other',           safe_POST_bool('stat_other'));
			set_block_setting($block_id, 'stat_media',           safe_POST_bool('stat_media'));
			set_block_setting($block_id, 'stat_repo',            safe_POST_bool('stat_repo'));
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
			exit;
		}

		require_once WT_ROOT.'includes/functions/functions_edit.php';

		$show_last_update=get_block_setting($block_id, 'show_last_update', true);
		echo '<tr><td class="descriptionbox wrap width33">';
		echo /* I18N: label for yes/no option */ WT_I18N::translate('Show date of last update?');
		echo '</td><td class="optionbox">';
		echo edit_field_yes_no('show_last_update', $show_last_update);
		echo '</td></tr>';

		$show_common_surnames=get_block_setting($block_id, 'show_common_surnames', true);
		echo '<tr><td class="descriptionbox wrap width33">';
		echo WT_I18N::translate('Show common surnames?');
		echo '</td><td class="optionbox">';
		echo edit_field_yes_no('show_common_surnames', $show_common_surnames);
		echo '</td></tr>';

		$stat_indi           =get_block_setting($block_id, 'stat_indi',            true);
		$stat_fam            =get_block_setting($block_id, 'stat_fam',             true);
		$stat_sour           =get_block_setting($block_id, 'stat_sour',            true);
		$stat_other          =get_block_setting($block_id, 'stat_other',           true);
		$stat_media          =get_block_setting($block_id, 'stat_media',           true);
		$stat_repo           =get_block_setting($block_id, 'stat_repo',            true);
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
	<td class="descriptionbox wrap width33"><?php echo WT_I18N::translate('Select the stats to show in this block'); ?></td>
	<td class="optionbox">
	<table>
		<tr>
			<td><input type="checkbox" value="yes" name="stat_indi"
			<?php if ($stat_indi) echo ' checked="checked"'; ?>>
			<?php echo WT_I18N::translate('Individuals'); ?></td>
			<td><input type="checkbox" value="yes" name="stat_first_birth"
			<?php if ($stat_first_birth) echo ' checked="checked"'; ?>>
			<?php echo WT_I18N::translate('Earliest birth year'); ?></td>
		</tr>
		<tr>
			<td><input type="checkbox" value="yes" name="stat_surname"
			<?php if ($stat_surname) echo ' checked="checked"'; ?>>
			<?php echo WT_I18N::translate('Total surnames'); ?></td>
			<td><input type="checkbox" value="yes" name="stat_last_birth"
			<?php if ($stat_last_birth) echo ' checked="checked"'; ?>>
			<?php echo WT_I18N::translate('Latest birth year'); ?></td>
		</tr>
		<tr>
			<td><input type="checkbox" value="yes" name="stat_fam"
			<?php if ($stat_fam) echo ' checked="checked"'; ?>>
			<?php echo WT_I18N::translate('Families'); ?></td>
			<td><input type="checkbox" value="yes" name="stat_first_death"
			<?php if ($stat_first_death) echo ' checked="checked"'; ?>>
			<?php echo WT_I18N::translate('Earliest death year'); ?></td>
		</tr>
		<tr>
			<td><input type="checkbox" value="yes" name="stat_sour"
			<?php if ($stat_sour) echo ' checked="checked"'; ?>>
			<?php echo WT_I18N::translate('Sources'); ?></td>
			<td><input type="checkbox" value="yes" name="stat_last_death"
			<?php if ($stat_last_death) echo ' checked="checked"'; ?>>
			<?php echo WT_I18N::translate('Latest death year'); ?></td>
		</tr>
		<tr>
			<td><input type="checkbox" value="yes" name="stat_media"
			<?php if ($stat_media) echo ' checked="checked"'; ?>>
			<?php echo WT_I18N::translate('Media objects'); ?></td>
			<td><input type="checkbox" value="yes" name="stat_long_life"
			<?php if ($stat_long_life) echo ' checked="checked"'; ?>>
			<?php echo WT_I18N::translate('Person who lived the longest'); ?></td>
		</tr>
		<tr>
			<td><input type="checkbox" value="yes" name="stat_repo"
			<?php if ($stat_repo) echo ' checked="checked"'; ?>>
			<?php echo WT_I18N::translate('Repositories'); ?></td>
			<td><input type="checkbox" value="yes" name="stat_avg_life"
			<?php if ($stat_avg_life) echo ' checked="checked"'; ?>>
			<?php echo WT_I18N::translate('Average age at death'); ?></td>
		</tr>
		<tr>
			<td><input type="checkbox" value="yes" name="stat_other"
			<?php if ($stat_other) echo ' checked="checked"'; ?>>
			<?php echo WT_I18N::translate('Other records'); ?></td>
			<td><input type="checkbox" value="yes" name="stat_most_chil"
			<?php if ($stat_most_chil) echo ' checked="checked"'; ?>>
			<?php echo WT_I18N::translate('Family with the most children'); ?></td>
		</tr>
		<tr>
			<td><input type="checkbox" value="yes" name="stat_events"
			<?php if ($stat_events) echo ' checked="checked"'; ?>>
			<?php echo WT_I18N::translate('Total events'); ?></td>
			<td><input type="checkbox" value="yes" name="stat_avg_chil"
			<?php if ($stat_avg_chil) echo ' checked="checked"'; ?>>
			<?php echo WT_I18N::translate('Average number of children per family'); ?></td>
		</tr>
		<tr>
			<td><input type="checkbox" value="yes" name="stat_users"
			<?php if ($stat_users) echo ' checked="checked"'; ?>>
			<?php echo WT_I18N::translate('Total users'); ?></td>
			<td>&nbsp;</td>
		</tr>
	</table>
	</td>
	</tr>
	<?php
		$stat_link=get_block_setting($block_id, 'stat_link', true);
		echo '<tr><td class="descriptionbox wrap width33">';
		echo WT_I18N::translate('Show link to Statistics charts?');
		echo '</td><td class="optionbox">';
		echo edit_field_yes_no('stat_link', $stat_link);
		echo '</td></tr>';
	}
}
