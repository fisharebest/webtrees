<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2017 webtrees development team
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
namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Bootstrap4;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\FontAwesome;
use Fisharebest\Webtrees\Functions\FunctionsDb;
use Fisharebest\Webtrees\Functions\FunctionsPrintLists;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module;
use Fisharebest\Webtrees\Query\QueryName;
use Fisharebest\Webtrees\Stats;
use Fisharebest\Webtrees\Theme;

/**
 * Class FamilyTreeStatisticsModule
 */
class FamilyTreeStatisticsModule extends AbstractModule implements ModuleBlockInterface {
	/** Show this number of surnames by default */
	const DEFAULT_NUMBER_OF_SURNAMES = 10;

	/** {@inheritdoc} */
	public function getTitle() {
		return /* I18N: Name of a module */ I18N::translate('Statistics');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return /* I18N: Description of “Statistics” module */ I18N::translate('The size of the family tree, earliest and latest events, common names, etc.');
	}

	/**
	 * Generate the HTML content of this block.
	 *
	 * @param int      $block_id
	 * @param bool     $template
	 * @param string[] $cfg
	 *
	 * @return string
	 */
	public function getBlock($block_id, $template = true, $cfg = []) {
		global $WT_TREE, $ctype;

		$show_last_update     = $this->getBlockSetting($block_id, 'show_last_update', '1');
		$show_common_surnames = $this->getBlockSetting($block_id, 'show_common_surnames', '1');
		$number_of_surnames   = $this->getBlockSetting($block_id, 'number_of_surnames', self::DEFAULT_NUMBER_OF_SURNAMES);
		$stat_indi            = $this->getBlockSetting($block_id, 'stat_indi', '1');
		$stat_fam             = $this->getBlockSetting($block_id, 'stat_fam', '1');
		$stat_sour            = $this->getBlockSetting($block_id, 'stat_sour', '1');
		$stat_media           = $this->getBlockSetting($block_id, 'stat_media', '1');
		$stat_repo            = $this->getBlockSetting($block_id, 'stat_repo', '1');
		$stat_surname         = $this->getBlockSetting($block_id, 'stat_surname', '1');
		$stat_events          = $this->getBlockSetting($block_id, 'stat_events', '1');
		$stat_users           = $this->getBlockSetting($block_id, 'stat_users', '1');
		$stat_first_birth     = $this->getBlockSetting($block_id, 'stat_first_birth', '1');
		$stat_last_birth      = $this->getBlockSetting($block_id, 'stat_last_birth', '1');
		$stat_first_death     = $this->getBlockSetting($block_id, 'stat_first_death', '1');
		$stat_last_death      = $this->getBlockSetting($block_id, 'stat_last_death', '1');
		$stat_long_life       = $this->getBlockSetting($block_id, 'stat_long_life', '1');
		$stat_avg_life        = $this->getBlockSetting($block_id, 'stat_avg_life', '1');
		$stat_most_chil       = $this->getBlockSetting($block_id, 'stat_most_chil', '1');
		$stat_avg_chil        = $this->getBlockSetting($block_id, 'stat_avg_chil', '1');

		foreach (['show_common_surnames', 'number_common_surnames', 'stat_indi', 'stat_fam', 'stat_sour', 'stat_media', 'stat_surname', 'stat_events', 'stat_users', 'stat_first_birth', 'stat_last_birth', 'stat_first_death', 'stat_last_death', 'stat_long_life', 'stat_avg_life', 'stat_most_chil', 'stat_avg_chil'] as $name) {
			if (array_key_exists($name, $cfg)) {
				$$name = $cfg[$name];
			}
		}

		$id    = $this->getName() . $block_id;
		$class = $this->getName() . '_block';
		if ($ctype === 'gedcom' && Auth::isManager($WT_TREE) || $ctype === 'user' && Auth::check()) {
			$title = FontAwesome::linkIcon('preferences', I18N::translate('Preferences'), ['class' => 'btn btn-link', 'href' => 'block_edit.php?block_id=' . $block_id . '&ged=' . $WT_TREE->getNameHtml() . '&ctype=' . $ctype]) . ' ';
		} else {
			$title = '';
		}
		$title .= $this->getTitle() . ' — ' . $WT_TREE->getTitleHtml();

		$stats = new Stats($WT_TREE);

		$content = '';

		if ($show_last_update) {
			$content .= '<p>' . /* I18N: %s is a date */
				I18N::translate('This family tree was last updated on %s.', strip_tags($stats->gedcomUpdated())) . '</p>';
		}

		/** Responsive Design */
		$content .= '<div class="stat-table1">';
		if ($stat_indi) {
			$content .= '<div class="stat-row"><div class="facts_label stat-cell">' . I18N::translate('Individuals') . '</div><div class="facts_value stats_value stat-cell"><a href="' . 'indilist.php?surname_sublist=no&amp;ged=' . $WT_TREE->getNameUrl() . '">' . $stats->totalIndividuals() . '</a></div></div>';
			$content .= '<div class="stat-row"><div class="facts_label stat-cell">' . I18N::translate('Males') . '</div><div class="facts_value stats_value stat-cell">' . $stats->totalSexMales() . '<br>' . $stats->totalSexMalesPercentage() . '</div></div>';
			$content .= '<div class="stat-row"><div class="facts_label stat-cell">' . I18N::translate('Females') . '</div><div class="facts_value stats_value stat-cell">' . $stats->totalSexFemales() . '<br>' . $stats->totalSexFemalesPercentage() . '</div></div>';
		}
		if ($stat_surname) {
			$content .= '<div class="stat-row"><div class="facts_label stat-cell">' . I18N::translate('Total surnames') . '</div><div class="facts_value stats_value stat-cell"><a href="indilist.php?show_all=yes&amp;surname_sublist=yes&amp;ged=' . $WT_TREE->getNameUrl() . '">' . $stats->totalSurnames() . '</a></div></div>';
		}
		if ($stat_fam) {
			$content .= '<div class="stat-row"><div class="facts_label stat-cell">' . I18N::translate('Families') . '</div><div class="facts_value stats_value stat-cell"><a href="famlist.php?ged=' . $WT_TREE->getNameUrl() . '">' . $stats->totalFamilies() . '</a></div></div>';
		}
		if ($stat_sour) {
			$content .= '<div class="stat-row"><div class="facts_label stat-cell">' . I18N::translate('Sources') . '</div><div class="facts_value stats_value stat-cell"><a href="sourcelist.php?ged=' . $WT_TREE->getNameUrl() . '">' . $stats->totalSources() . '</a></div></div>';
		}
		if ($stat_media) {
			$content .= '<div class="stat-row"><div class="facts_label stat-cell">' . I18N::translate('Media objects') . '</div><div class="facts_value stats_value stat-cell"><a href="medialist.php?ged=' . $WT_TREE->getNameUrl() . '">' . $stats->totalMedia() . '</a></div></div>';
		}
		if ($stat_repo) {
			$content .= '<div class="stat-row"><div class="facts_label stat-cell">' . I18N::translate('Repositories') . '</div><div class="facts_value stats_value stat-cell"><a href="repolist.php?ged=' . $WT_TREE->getNameUrl() . '">' . $stats->totalRepositories() . '</a></div></div>';
		}
		if ($stat_events) {
			$content .= '<div class="stat-row"><div class="facts_label stat-cell">' . I18N::translate('Total events') . '</div><div class="facts_value stats_value stat-cell">' . $stats->totalEvents() . '</div></div>';
		}
		if ($stat_users) {
			$content .= '<div class="stat-row"><div class="facts_label stat-cell">' . I18N::translate('Total users') . '</div><div class="facts_value stats_value stat-cell">';
			if (Auth::isManager($WT_TREE)) {
				$content .= '<a href="admin_users.php">' . $stats->totalUsers() . '</a>';
			} else {
				$content .= $stats->totalUsers();
			}
			$content .= '</div></div>';
		}
		$content .= '</div><div class="facts_table stat-table2">';
		if ($stat_first_birth) {
			$content .= '<div class="stat-row"><div class="facts_label stat-cell">' . I18N::translate('Earliest birth year') . '</div><div class="facts_value stats_value stat-cell">' . $stats->firstBirthYear() . '</div>';
			$content .= '<div class="facts_value stat-cell left">' . $stats->firstBirth() . '</div>';
			$content .= '</div>';
		}
		if ($stat_last_birth) {
			$content .= '<div class="stat-row"><div class="facts_label stat-cell">' . I18N::translate('Latest birth year') . '</div><div class="facts_value stats_value stat-cell">' . $stats->lastBirthYear() . '</div>';
			$content .= '<div class="facts_value stat-cell left">' . $stats->lastBirth() . '</div>';
			$content .= '</div>';
		}
		if ($stat_first_death) {
			$content .= '<div class="stat-row"><div class="facts_label stat-cell">' . I18N::translate('Earliest death year') . '</div><div class="facts_value stats_value stat-cell">' . $stats->firstDeathYear() . '</div>';
			$content .= '<div class="facts_value stat-cell left">' . $stats->firstDeath() . '</div>';
			$content .= '</div>';
		}
		if ($stat_last_death) {
			$content .= '<div class="stat-row"><div class="facts_label stat-cell">' . I18N::translate('Latest death year') . '</div><div class="facts_value stats_value stat-cell">' . $stats->lastDeathYear() . '</div>';
			$content .= '<div class="facts_value stat-cell left">' . $stats->lastDeath() . '</div>';
			$content .= '</div>';
		}
		if ($stat_long_life) {
			$content .= '<div class="stat-row"><div class="facts_label stat-cell">' . I18N::translate('Individual who lived the longest') . '</div><div class="facts_value stats_value stat-cell">' . $stats->longestLifeAge() . '</div>';
			$content .= '<div class="facts_value stat-cell left">' . $stats->longestLife() . '</div>';
			$content .= '</div>';
		}
		if ($stat_avg_life) {
			$content .= '<div class="stat-row"><div class="facts_label stat-cell">' . I18N::translate('Average age at death') . '</div><div class="facts_value stats_value stat-cell">' . $stats->averageLifespan() . '</div>';
			$content .= '<div class="facts_value stat-cell left">' . I18N::translate('Males') . ':&nbsp;' . $stats->averageLifespanMale();
			$content .= '&nbsp;&nbsp;&nbsp;' . I18N::translate('Females') . ':&nbsp;' . $stats->averageLifespanFemale() . '</div>';
			$content .= '</div>';
		}

		if ($stat_most_chil) {
			$content .= '<div class="stat-row"><div class="facts_label stat-cell">' . I18N::translate('Family with the most children') . '</div><div class="facts_value stats_value stat-cell">' . $stats->largestFamilySize() . '</div>';
			$content .= '<div class="facts_value stat-cell left">' . $stats->largestFamily() . '</div>';
			$content .= '</div>';
		}
		if ($stat_avg_chil) {
			$content .= '<div class="stat-row"><div class="facts_label stat-cell">' . I18N::translate('Average number of children per family') . '</div><div class="facts_value stats_value stat-cell">' . $stats->averageChildren() . '</div>';
			$content .= '<div class="facts_value stat-cell left"></div>';
			$content .= '</div>';
		}
		$content .= '</div>';

		if ($show_common_surnames) {
			$surnames = FunctionsDb::getTopSurnames($WT_TREE->getTreeId(), 0, (int) $number_of_surnames);

			$all_surnames = [];
			foreach (array_keys($surnames) as $surname) {
				$all_surnames = array_merge($all_surnames, QueryName::surnames($WT_TREE, $surname, '', false, false));
			}

			if (!empty($surnames)) {
				ksort($all_surnames);
				$content .= '<div class="clearfloat">';
				$content .= '<p>';
				$content .= '<strong>' . I18N::translate('Most common surnames') . '</strong>';
				$content .= '<br>';
				$content .= '<span class="common_surnames">' . FunctionsPrintLists::surnameList($all_surnames, 2, false, 'indilist.php', $WT_TREE) . '</span>';
				$content .= '</p>';
				$content .= '</div>';
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

	/**
	 * An HTML form to edit block settings
	 *
	 * @param int $block_id
	 */
	public function configureBlock($block_id) {
		if (Filter::postBool('save') && Filter::checkCsrf()) {
			$this->setBlockSetting($block_id, 'show_last_update', Filter::postBool('show_last_update'));
			$this->setBlockSetting($block_id, 'show_common_surnames', Filter::postBool('show_common_surnames'));
			$this->setBlockSetting($block_id, 'number_of_surnames', Filter::postInteger('number_of_surnames'));
			$this->setBlockSetting($block_id, 'stat_indi', Filter::postBool('stat_indi'));
			$this->setBlockSetting($block_id, 'stat_fam', Filter::postBool('stat_fam'));
			$this->setBlockSetting($block_id, 'stat_sour', Filter::postBool('stat_sour'));
			$this->setBlockSetting($block_id, 'stat_other', Filter::postBool('stat_other'));
			$this->setBlockSetting($block_id, 'stat_media', Filter::postBool('stat_media'));
			$this->setBlockSetting($block_id, 'stat_repo', Filter::postBool('stat_repo'));
			$this->setBlockSetting($block_id, 'stat_surname', Filter::postBool('stat_surname'));
			$this->setBlockSetting($block_id, 'stat_events', Filter::postBool('stat_events'));
			$this->setBlockSetting($block_id, 'stat_users', Filter::postBool('stat_users'));
			$this->setBlockSetting($block_id, 'stat_first_birth', Filter::postBool('stat_first_birth'));
			$this->setBlockSetting($block_id, 'stat_last_birth', Filter::postBool('stat_last_birth'));
			$this->setBlockSetting($block_id, 'stat_first_death', Filter::postBool('stat_first_death'));
			$this->setBlockSetting($block_id, 'stat_last_death', Filter::postBool('stat_last_death'));
			$this->setBlockSetting($block_id, 'stat_long_life', Filter::postBool('stat_long_life'));
			$this->setBlockSetting($block_id, 'stat_avg_life', Filter::postBool('stat_avg_life'));
			$this->setBlockSetting($block_id, 'stat_most_chil', Filter::postBool('stat_most_chil'));
			$this->setBlockSetting($block_id, 'stat_avg_chil', Filter::postBool('stat_avg_chil'));
		}

		$show_last_update     = $this->getBlockSetting($block_id, 'show_last_update', '1');
		$show_common_surnames = $this->getBlockSetting($block_id, 'show_common_surnames', '1');
		$number_of_surnames   = $this->getBlockSetting($block_id, 'number_of_surnames', self::DEFAULT_NUMBER_OF_SURNAMES);
		$stat_indi            = $this->getBlockSetting($block_id, 'stat_indi', '1');
		$stat_fam             = $this->getBlockSetting($block_id, 'stat_fam', '1');
		$stat_sour            = $this->getBlockSetting($block_id, 'stat_sour', '1');
		$stat_media           = $this->getBlockSetting($block_id, 'stat_media', '1');
		$stat_repo            = $this->getBlockSetting($block_id, 'stat_repo', '1');
		$stat_surname         = $this->getBlockSetting($block_id, 'stat_surname', '1');
		$stat_events          = $this->getBlockSetting($block_id, 'stat_events', '1');
		$stat_users           = $this->getBlockSetting($block_id, 'stat_users', '1');
		$stat_first_birth     = $this->getBlockSetting($block_id, 'stat_first_birth', '1');
		$stat_last_birth      = $this->getBlockSetting($block_id, 'stat_last_birth', '1');
		$stat_first_death     = $this->getBlockSetting($block_id, 'stat_first_death', '1');
		$stat_last_death      = $this->getBlockSetting($block_id, 'stat_last_death', '1');
		$stat_long_life       = $this->getBlockSetting($block_id, 'stat_long_life', '1');
		$stat_avg_life        = $this->getBlockSetting($block_id, 'stat_avg_life', '1');
		$stat_most_chil       = $this->getBlockSetting($block_id, 'stat_most_chil', '1');
		$stat_avg_chil        = $this->getBlockSetting($block_id, 'stat_avg_chil', '1');

		?>
		<fieldset class="form-group">
			<div class="row">
				<legend class="col-form-legend col-sm-3">
					<?= I18N::translate('Last change') ?>
				</legend>
				<div class="col-sm-9">
					<?= Bootstrap4::checkbox(/* I18N: label for yes/no option */ I18N::translate('Show date of last update'), false, ['name' => 'show_last_update', 'checked' => (bool) $show_last_update]) ?>
				</div>
			</div>
		</fieldset>

		<fieldset class="form-group">
			<div class="row">
				<legend class="col-form-legend col-sm-3">
					<?= I18N::translate('Statistics') ?>
				</legend>
				<div class="col-sm-9">
					<?= Bootstrap4::checkbox(I18N::translate('Individuals'), false, ['name' => 'stat_indi', 'checked' => (bool) $stat_indi]) ?>
					<?= Bootstrap4::checkbox(I18N::translate('Total surnames'), false, ['name' => 'stat_surname', 'checked' => (bool) $stat_surname]) ?>
					<?= Bootstrap4::checkbox(I18N::translate('Families'), false, ['name' => 'stat_fam', 'checked' => (bool) $stat_fam]) ?>
					<?= Bootstrap4::checkbox(I18N::translate('Sources'), false, ['name' => 'stat_sour', 'checked' => (bool) $stat_sour]) ?>
					<?= Bootstrap4::checkbox(I18N::translate('Media objects'), false, ['name' => 'stat_media', 'checked' => (bool) $stat_media]) ?>
					<?= Bootstrap4::checkbox(I18N::translate('Repositories'), false, ['name' => 'stat_repo', 'checked' => (bool) $stat_repo]) ?>
					<?= Bootstrap4::checkbox(I18N::translate('Total events'), false, ['name' => 'stat_events', 'checked' => (bool) $stat_events]) ?>
					<?= Bootstrap4::checkbox(I18N::translate('Total users'), false, ['name' => 'stat_users', 'checked' => (bool) $stat_users]) ?>
					<?= Bootstrap4::checkbox(I18N::translate('Earliest birth year'), false, ['name' => 'stat_first_birth', 'checked' => (bool) $stat_first_birth]) ?>
					<?= Bootstrap4::checkbox(I18N::translate('Latest birth year'), false, ['name' => 'stat_last_birth', 'checked' => (bool) $stat_last_birth]) ?>
					<?= Bootstrap4::checkbox(I18N::translate('Earliest death year'), false, ['name' => 'stat_first_death', 'checked' => (bool) $stat_first_death]) ?>
					<?= Bootstrap4::checkbox(I18N::translate('Latest death year'), false, ['name' => 'stat_last_death', 'checked' => (bool) $stat_last_death]) ?>
					<?= Bootstrap4::checkbox(I18N::translate('Individual who lived the longest'), false, ['name' => 'stat_long_life', 'checked' => (bool) $stat_long_life]) ?>
					<?= Bootstrap4::checkbox(I18N::translate('Average age at death'), false, ['name' => 'stat_avg_life', 'checked' => (bool) $stat_avg_life]) ?>
					<?= Bootstrap4::checkbox(I18N::translate('Family with the most children'), false, ['name' => 'stat_most_chil', 'checked' => (bool) $stat_most_chil]) ?>
					<?= Bootstrap4::checkbox(I18N::translate('Average number of children per family'), false, ['name' => 'stat_avg_chil', 'checked' => (bool) $stat_avg_chil]) ?>
				</div>
			</div>
		</fieldset>

		<fieldset class="form-group">
			<div class="row">
				<legend class="col-form-legend col-sm-3">
					<label for="show_common_surnames">
						<?= I18N::translate('Surnames') ?>
					</label>
				</legend>
				<div class="col-sm-9">
					<?= Bootstrap4::checkbox(I18N::translate('Most common surnames'), false, ['name' => 'show_common_surnames', 'checked' => (bool) $show_common_surnames]) ?>
					<label for="number_of_surnames">
						<?= /* I18N: ... to show in a list */ I18N::translate('Number of surnames') ?>
						<input
							class="form-control"
							id="number_of_surnames"
							maxlength="5"
							name="number_of_surnames"
							pattern="[1-9][0-9]*"
							required
							type="text"
							value="<?= Filter::escapeHtml($number_of_surnames) ?>"
						>
					</label>
				</div>
			</div>
		</fieldset>
		<?php
	}
}
