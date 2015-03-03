<?php
namespace Fisharebest\Webtrees;

/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Class FamilyTreeStatisticsModule
 */
class FamilyTreeStatisticsModule extends Module implements ModuleBlockInterface {
	/** {@inheritdoc} */
	public function getTitle() {
		return /* I18N: Name of a module */ I18N::translate('Statistics');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return /* I18N: Description of “Statistics” module */ I18N::translate('The size of the family tree, earliest and latest events, common names, etc.');
	}

	/** {@inheritdoc} */
	public function getBlock($block_id, $template = true, $cfg = null) {
		global $WT_TREE, $ctype;

		$show_last_update     = get_block_setting($block_id, 'show_last_update', '1');
		$show_common_surnames = get_block_setting($block_id, 'show_common_surnames', '1');
		$stat_indi            = get_block_setting($block_id, 'stat_indi', '1');
		$stat_fam             = get_block_setting($block_id, 'stat_fam', '1');
		$stat_sour            = get_block_setting($block_id, 'stat_sour', '1');
		$stat_media           = get_block_setting($block_id, 'stat_media', '1');
		$stat_repo            = get_block_setting($block_id, 'stat_repo', '1');
		$stat_surname         = get_block_setting($block_id, 'stat_surname', '1');
		$stat_events          = get_block_setting($block_id, 'stat_events', '1');
		$stat_users           = get_block_setting($block_id, 'stat_users', '1');
		$stat_first_birth     = get_block_setting($block_id, 'stat_first_birth', '1');
		$stat_last_birth      = get_block_setting($block_id, 'stat_last_birth', '1');
		$stat_first_death     = get_block_setting($block_id, 'stat_first_death', '1');
		$stat_last_death      = get_block_setting($block_id, 'stat_last_death', '1');
		$stat_long_life       = get_block_setting($block_id, 'stat_long_life', '1');
		$stat_avg_life        = get_block_setting($block_id, 'stat_avg_life', '1');
		$stat_most_chil       = get_block_setting($block_id, 'stat_most_chil', '1');
		$stat_avg_chil        = get_block_setting($block_id, 'stat_avg_chil', '1');

		// This can be overriden when embedding in an HTML block
		$block     = '0';
		$stat_link = '1';

		if ($cfg) {
			foreach (array('show_common_surnames', 'stat_indi', 'stat_fam', 'stat_sour', 'stat_media', 'stat_surname', 'stat_events', 'stat_users', 'stat_first_birth', 'stat_last_birth', 'stat_first_death', 'stat_last_death', 'stat_long_life', 'stat_avg_life', 'stat_most_chil', 'stat_avg_chil', 'stat_link', 'block') as $name) {
				if (array_key_exists($name, $cfg)) {
					$$name = $cfg[$name];
				}
			}
		}

		$id = $this->getName() . $block_id;
		$class = $this->getName() . '_block';
		if ($ctype === 'gedcom' && WT_USER_GEDCOM_ADMIN || $ctype === 'user' && Auth::check()) {
			$title = '<i class="icon-admin" title="' . I18N::translate('Configure') . '" onclick="modalDialog(\'block_edit.php?block_id=' . $block_id . '\', \'' . $this->getTitle() . '\');"></i>';
		} else {
			$title = '';
		}
		$title .= $this->getTitle();

		$stats = new Stats($WT_TREE);

		$content = '<b>' . WT_TREE_TITLE . '</b><br>';

		if ($show_last_update) {
			$content .= '<div>' . /* I18N: %s is a date */ I18N::translate('This family tree was last updated on %s.', strip_tags($stats->gedcomUpdated())) . '</div>';
		}
/** Responsive Design */

	$content .= '<div class="stat-table1">';
		if ($stat_indi) {
			$content .= '<div class="stat-row"><div class="facts_label stat-cell">' . I18N::translate('Individuals') .    '</div><div class="facts_value stats_value stat-cell"><a href="' . "indilist.php?surname_sublist=no&amp;ged=" . WT_GEDURL . '">' . $stats->totalIndividuals() . '</a></div></div>';
			$content .= '<div class="stat-row"><div class="facts_label stat-cell">' . I18N::translate('Males') .          '</div><div class="facts_value stats_value stat-cell">' . $stats->totalSexMales() . '<br>' . $stats->totalSexMalesPercentage() . '</a></div></div>';
			$content .= '<div class="stat-row"><div class="facts_label stat-cell">' . I18N::translate('Females') .        '</div><div class="facts_value stats_value stat-cell">' . $stats->totalSexFemales() . '<br>' . $stats->totalSexFemalesPercentage() . '</a></div></div>';
		}
		if ($stat_surname) {
			$content .= '<div class="stat-row"><div class="facts_label stat-cell">' . I18N::translate('Total surnames') . '</div><div class="facts_value stats_value stat-cell"><a href="indilist.php?show_all=yes&amp;surname_sublist=yes&amp;ged=' . WT_GEDURL . '">' . $stats->totalSurnames() . '</a></div></div>';
		}
		if ($stat_fam) {
			$content .= '<div class="stat-row"><div class="facts_label stat-cell">' . I18N::translate('Families') .       '</div><div class="facts_value stats_value stat-cell"><a href="famlist.php?ged=' . WT_GEDURL . '">' . $stats->totalFamilies() . '</a></div></div>';
		}
		if ($stat_sour) {
			$content .= '<div class="stat-row"><div class="facts_label stat-cell">' . I18N::translate('Sources') .        '</div><div class="facts_value stats_value stat-cell"><a href="sourcelist.php?ged=' . WT_GEDURL . '">' . $stats->totalSources() . '</a></div></div>';
		}
		if ($stat_media) {
			$content .= '<div class="stat-row"><div class="facts_label stat-cell">' . I18N::translate('Media objects') .  '</div><div class="facts_value stats_value stat-cell"><a href="medialist.php?ged=' . WT_GEDURL . '">' . $stats->totalMedia() . '</a></div></div>';
		}
		if ($stat_repo) {
			$content .= '<div class="stat-row"><div class="facts_label stat-cell">' . I18N::translate('Repositories') .   '</div><div class="facts_value stats_value stat-cell"><a href="repolist.php?ged=' . WT_GEDURL . '">' . $stats->totalRepositories() . '</a></div></div>';
		}
		if ($stat_events) {
			$content .= '<div class="stat-row"><div class="facts_label stat-cell">' . I18N::translate('Total events') .    '</div><div class="facts_value stats_value stat-cell">' . $stats->totalEvents() . '</div></div>';
		}
		if ($stat_users) {
			$content .= '<div class="stat-row"><div class="facts_label stat-cell">' . I18N::translate('Total users') .     '</div><div class="facts_value stats_value stat-cell">';
			if (WT_USER_GEDCOM_ADMIN) {
				$content .= '<a href="admin_users.php">' . $stats->totalUsers() . '</a>';
			} else {
				$content .= $stats->totalUsers();
			}
			$content .= '</div></div>';
		}
		if (!$block) {
			$content .= '</div><div class="facts_table stat-table2">';
		}
		if ($stat_first_birth) {
			$content .= '<div class="stat-row"><div class="facts_label stat-cell">' . I18N::translate('Earliest birth year') . '</div><div class="facts_value stats_value stat-cell">' . $stats->firstBirthYear() . '</div>';
			if (!$block) {
				$content .= '<div class="facts_value stat-cell left">' . $stats->firstBirth() . '</div>';
			}
			$content .= '</div>';
		}
		if ($stat_last_birth) {
			$content .= '<div class="stat-row"><div class="facts_label stat-cell">' . I18N::translate('Latest birth year') . '</div><div class="facts_value stats_value stat-cell">' . $stats->lastBirthYear() . '</div>';
			if (!$block) {
				$content .= '<div class="facts_value stat-cell left">' . $stats->lastBirth() . '</div>';
			}
			$content .= '</div>';
		}
		if ($stat_first_death) {
			$content .= '<div class="stat-row"><div class="facts_label stat-cell">' . I18N::translate('Earliest death year') . '</div><div class="facts_value stats_value stat-cell">' . $stats->firstDeathYear() . '</div>';
			if (!$block) {
				$content .= '<div class="facts_value stat-cell left">' . $stats->firstDeath() . '</div>';
			}
			$content .= '</div>';
		}
		if ($stat_last_death) {
			$content .= '<div class="stat-row"><div class="facts_label stat-cell">' . I18N::translate('Latest death year') . '</div><div class="facts_value stats_value stat-cell">' . $stats->lastDeathYear() . '</div>';
			if (!$block) {
				$content .= '<div class="facts_value stat-cell left">' . $stats->lastDeath() . '</div>';
			}
			$content .= '</div>';
		}
		if ($stat_long_life) {
			$content .= '<div class="stat-row"><div class="facts_label stat-cell">' . I18N::translate('Individual who lived the longest') . '</div><div class="facts_value stats_value stat-cell">' . $stats->LongestLifeAge() . '</div>';
			if (!$block) {
				$content .= '<div class="facts_value stat-cell left">' . $stats->LongestLife() . '</div>';
			}
			$content .= '</div>';
		}
		if ($stat_avg_life) {
			$content .= '<div class="stat-row"><div class="facts_label stat-cell">' . I18N::translate('Average age at death') . '</div><div class="facts_value stats_value stat-cell">' . $stats->averageLifespan() . '</div>';
			if (!$block) {
				$content .= '<div class="facts_value stat-cell left">' . I18N::translate('Males') . ':&nbsp;' . $stats->averageLifespanMale();
				$content .= '&nbsp;&nbsp;&nbsp;' . I18N::translate('Females') . ':&nbsp;' . $stats->averageLifespanFemale() . '</div>';
			}
			$content .= '</div>';
		}

		if ($stat_most_chil && !$block) {
			$content .= '<div class="stat-row"><div class="facts_label stat-cell">' . I18N::translate('Family with the most children') . '</div><div class="facts_value stats_value  stat-cell">' . $stats->largestFamilySize() . '</div>';
			if (!$block) {
				$content .= '<div class="facts_value stat-cell left">' . $stats->largestFamily() . '</div>';
			}
			$content .= '</div>';
		}
		if ($stat_avg_chil) {
			$content .= '<div class="stat-row"><div class="facts_label stat-cell">' . I18N::translate('Average number of children per family') . '</div><div class="facts_value stats_value  stat-cell">' . $stats->averageChildren() . '</div>';
			if (!$block) {
				$content .= '<div class="facts_value stat-cell left"></div>';
			}
			$content .= '</div>';
		}
		if ($stat_link) {
			$content .= '</div><div class="clearfloat"><a href="statistics.php?ged=' . WT_GEDURL . '"><b>' . I18N::translate('View statistics as graphs') . '</b></a></div>';
		}
		// NOTE: Print the most common surnames
		if ($show_common_surnames) {
			$surnames = get_common_surnames($WT_TREE->getPreference('COMMON_NAMES_THRESHOLD'), $WT_TREE);
			if (count($surnames) > 0) {
				$content .= '<p><b>' . I18N::translate('Most common surnames') . '</b></p>';
				$content .= '<div class="common_surnames">';
				$i = 0;
				foreach ($surnames as $indexval => $surname) {
					if (stristr($surname['name'], '@N.N') === false) {
						if ($i > 0) {
							$content .= ', ';
						}
						$content .= '<a href="' . "indilist.php?ged=" . WT_GEDURL . "&amp;surname=" . rawurlencode($surname['name']) . '">' . $surname['name'] . '</a>';
						$i++;
					}
				}
			}
		}

		if ($template) {
			return Theme::theme()->formatBlock($id, $title, $class, $content);
		} else {
			return $content;
		}
	}

	/** {@inheritdoc} */
	public function loadAjax() {
		return true;
	}

	/** {@inheritdoc} */
	public function isUserBlock() {
		return true;
	}

	/** {@inheritdoc} */
	public function isGedcomBlock() {
		return true;
	}

	/** {@inheritdoc} */
	public function configureBlock($block_id) {
		if (Filter::postBool('save') && Filter::checkCsrf()) {
			set_block_setting($block_id, 'show_last_update', Filter::postBool('show_last_update'));
			set_block_setting($block_id, 'show_common_surnames', Filter::postBool('show_common_surnames'));
			set_block_setting($block_id, 'stat_indi', Filter::postBool('stat_indi'));
			set_block_setting($block_id, 'stat_fam', Filter::postBool('stat_fam'));
			set_block_setting($block_id, 'stat_sour', Filter::postBool('stat_sour'));
			set_block_setting($block_id, 'stat_other', Filter::postBool('stat_other'));
			set_block_setting($block_id, 'stat_media', Filter::postBool('stat_media'));
			set_block_setting($block_id, 'stat_repo', Filter::postBool('stat_repo'));
			set_block_setting($block_id, 'stat_surname', Filter::postBool('stat_surname'));
			set_block_setting($block_id, 'stat_events', Filter::postBool('stat_events'));
			set_block_setting($block_id, 'stat_users', Filter::postBool('stat_users'));
			set_block_setting($block_id, 'stat_first_birth', Filter::postBool('stat_first_birth'));
			set_block_setting($block_id, 'stat_last_birth', Filter::postBool('stat_last_birth'));
			set_block_setting($block_id, 'stat_first_death', Filter::postBool('stat_first_death'));
			set_block_setting($block_id, 'stat_last_death', Filter::postBool('stat_last_death'));
			set_block_setting($block_id, 'stat_long_life', Filter::postBool('stat_long_life'));
			set_block_setting($block_id, 'stat_avg_life', Filter::postBool('stat_avg_life'));
			set_block_setting($block_id, 'stat_most_chil', Filter::postBool('stat_most_chil'));
			set_block_setting($block_id, 'stat_avg_chil', Filter::postBool('stat_avg_chil'));
		}

		$show_last_update     = get_block_setting($block_id, 'show_last_update', '1');
		$show_common_surnames = get_block_setting($block_id, 'show_common_surnames', '1');
		$stat_indi            = get_block_setting($block_id, 'stat_indi', '1');
		$stat_fam             = get_block_setting($block_id, 'stat_fam', '1');
		$stat_sour            = get_block_setting($block_id, 'stat_sour', '1');
		$stat_media           = get_block_setting($block_id, 'stat_media', '1');
		$stat_repo            = get_block_setting($block_id, 'stat_repo', '1');
		$stat_surname         = get_block_setting($block_id, 'stat_surname', '1');
		$stat_events          = get_block_setting($block_id, 'stat_events', '1');
		$stat_users           = get_block_setting($block_id, 'stat_users', '1');
		$stat_first_birth     = get_block_setting($block_id, 'stat_first_birth', '1');
		$stat_last_birth      = get_block_setting($block_id, 'stat_last_birth', '1');
		$stat_first_death     = get_block_setting($block_id, 'stat_first_death', '1');
		$stat_last_death      = get_block_setting($block_id, 'stat_last_death', '1');
		$stat_long_life       = get_block_setting($block_id, 'stat_long_life', '1');
		$stat_avg_life        = get_block_setting($block_id, 'stat_avg_life', '1');
		$stat_most_chil       = get_block_setting($block_id, 'stat_most_chil', '1');
		$stat_avg_chil        = get_block_setting($block_id, 'stat_avg_chil', '1');

		echo '<tr><td class="descriptionbox wrap width33">';
		echo /* I18N: label for yes/no option */ I18N::translate('Show date of last update?');
		echo '</td><td class="optionbox">';
		echo edit_field_yes_no('show_last_update', $show_last_update);
		echo '</td></tr>';

		echo '<tr><td class="descriptionbox wrap width33">';
		echo I18N::translate('Show common surnames?');
		echo '</td><td class="optionbox">';
		echo edit_field_yes_no('show_common_surnames', $show_common_surnames);
		echo '</td></tr>';

?>
	<tr>
	<td class="descriptionbox wrap width33"><?php echo I18N::translate('Select the stats to show in this block'); ?></td>
	<td class="optionbox">
	<table>
		<tbody>
			<tr>
				<td>
					<label>
						<input type="checkbox" value="yes" name="stat_indi" <?php echo $stat_indi ? 'checked' : ''; ?>>
						<?php echo I18N::translate('Individuals'); ?>
					</label>
				</td>
				<td>
					<label>
						<input type="checkbox" value="yes" name="stat_first_birth" <?php echo $stat_first_birth ? 'checked' : ''; ?>>
						<?php echo I18N::translate('Earliest birth year'); ?>
					</label>
				</td>
			</tr>
			<tr>
				<td>
					<label>
						<input type="checkbox" value="yes" name="stat_surname" <?php echo $stat_surname ? 'checked' : ''; ?>>
						<?php echo I18N::translate('Total surnames'); ?>
					</label>
				</td>
				<td>
					<label>
						<input type="checkbox" value="yes" name="stat_last_birth" <?php echo $stat_last_birth ? 'checked' : ''; ?>>
						<?php echo I18N::translate('Latest birth year'); ?>
					</label>
				</td>
			</tr>
			<tr>
				<td>
					<label>
						<input type="checkbox" value="yes" name="stat_fam" <?php echo $stat_fam ? 'checked' : ''; ?>>
						<?php echo I18N::translate('Families'); ?>
					</label>
				</td>
				<td>
					<label>
						<input type="checkbox" value="yes" name="stat_first_death" <?php echo $stat_first_death ? 'checked' : ''; ?>>
						<?php echo I18N::translate('Earliest death year'); ?>
					</label>
				</td>
			</tr>
			<tr>
				<td>
					<label>
						<input type="checkbox" value="yes" name="stat_sour" <?php echo $stat_sour ? 'checked' : ''; ?>>
						<?php echo I18N::translate('Sources'); ?>
					</label>
				</td>
				<td>
					<label>
						<input type="checkbox" value="yes" name="stat_last_death" <?php echo $stat_last_death ? 'checked' : ''; ?>>
						<?php echo I18N::translate('Latest death year'); ?>
					</label>
				</td>
			</tr>
			<tr>
				<td>
					<label>
						<input type="checkbox" value="yes" name="stat_media" <?php echo $stat_media ? 'checked' : ''; ?>>
						<?php echo I18N::translate('Media objects'); ?>
					</label>
				</td>
				<td>
					<label>
						<input type="checkbox" value="yes" name="stat_long_life" <?php echo $stat_long_life ? 'checked' : ''; ?>>
						<?php echo I18N::translate('Individual who lived the longest'); ?>
					</label>
				</td>
			</tr>
			<tr>
				<td>
					<label>
						<input type="checkbox" value="yes" name="stat_repo" <?php echo $stat_repo ? 'checked' : ''; ?>>
						<?php echo I18N::translate('Repositories'); ?>
					</label>
				</td>
				<td>
					<label>
						<input type="checkbox" value="yes" name="stat_avg_life" <?php echo $stat_avg_life ? 'checked' : ''; ?>>
						<?php echo I18N::translate('Average age at death'); ?>
					</label>
				</td>
			</tr>
			<tr>
				<td>
					<label>
						<input type="checkbox" value="yes" name="stat_events" <?php echo $stat_events ? 'checked' : ''; ?>>
						<?php echo I18N::translate('Total events'); ?>
					</label>
				</td>
				<td>
					<label>
						<input type="checkbox" value="yes" name="stat_most_chil" <?php echo $stat_most_chil ? 'checked' : ''; ?>>
						<?php echo I18N::translate('Family with the most children'); ?>
					</label>
				</td>
			</tr>
			<tr>
				<td>
					<label>
						<input type="checkbox" value="yes" name="stat_users" <?php echo $stat_users ? 'checked' : ''; ?>>
						<?php echo I18N::translate('Total users'); ?>
					</label>
				</td>
				<td>
					<label>
						<input type="checkbox" value="yes" name="stat_avg_chil" <?php echo $stat_avg_chil ? 'checked' : ''; ?>>
						<?php echo I18N::translate('Average number of children per family'); ?>
					</label>
				</td>
			</tr>
		</tbody>
	</table>
	</td>
	</tr>
	<?php
	}
}
