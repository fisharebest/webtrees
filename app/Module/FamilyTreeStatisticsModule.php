<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\Functions\FunctionsDb;
use Fisharebest\Webtrees\Functions\FunctionsPrintLists;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Query\QueryName;
use Fisharebest\Webtrees\Stats;

/**
 * Class FamilyTreeStatisticsModule
 */
class FamilyTreeStatisticsModule extends AbstractModule implements ModuleBlockInterface {
	/** Show this number of surnames by default */
	const DEFAULT_NUMBER_OF_SURNAMES = 10;

	/** {@inheritdoc} */
	public function getTitle() {
		return /* I18N: Name of a module */
			I18N::translate('Statistics');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return /* I18N: Description of “Statistics” module */
			I18N::translate('The size of the family tree, earliest and latest events, common names, etc.');
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
	public function getBlock($block_id, $template = true, $cfg = []): string {
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

		extract($cfg, EXTR_OVERWRITE);

		if ($show_common_surnames) {
			$surnames = FunctionsDb::getTopSurnames($WT_TREE->getTreeId(), 0, (int) $number_of_surnames);

			$all_surnames = [];
			foreach (array_keys($surnames) as $surname) {
				$all_surnames = array_merge($all_surnames, QueryName::surnames($WT_TREE, $surname, '', false, false));
			}
			ksort($all_surnames);

			$surnames = FunctionsPrintLists::surnameList($all_surnames, 2, false, 'individual-list', $WT_TREE);
		} else {
			$surnames = '';
		}

		$content = view('blocks/family-tree-statistics', [
			'show_last_update'     => $show_last_update,
			'show_common_surnames' => $show_common_surnames,
			'number_of_surnames'   => $number_of_surnames,
			'stat_indi'            => $stat_indi,
			'stat_fam'             => $stat_fam,
			'stat_sour'            => $stat_sour,
			'stat_media'           => $stat_media,
			'stat_repo'            => $stat_repo,
			'stat_surname'         => $stat_surname,
			'stat_events'          => $stat_events,
			'stat_users'           => $stat_users,
			'stat_first_birth'     => $stat_first_birth,
			'stat_last_birth'      => $stat_last_birth,
			'stat_first_death'     => $stat_first_death,
			'stat_last_death'      => $stat_last_death,
			'stat_long_life'       => $stat_long_life,
			'stat_avg_life'        => $stat_avg_life,
			'stat_most_chil'       => $stat_most_chil,
			'stat_avg_chil'        => $stat_avg_chil,
			'stats'                => new Stats($WT_TREE),
			'surnames'             => $surnames,
		]);

		if ($template) {
			if ($ctype === 'gedcom' && Auth::isManager($WT_TREE)) {
				$config_url = route('tree-page-block-edit', ['block_id' => $block_id, 'ged' => $WT_TREE->getName()]);
			} elseif ($ctype === 'user' && Auth::check()) {
				$config_url = route('user-page-block-edit', ['block_id' => $block_id, 'ged' => $WT_TREE->getName()]);
			} else {
				$config_url = '';
			}

			return view('blocks/template', [
				'block'      => str_replace('_', '-', $this->getName()),
				'id'         => $block_id,
				'config_url' => $config_url,
				'title'      => $this->getTitle(),
				'content'    => $content,
			]);
		} else {
			return $content;
		}
	}

	/** {@inheritdoc} */
	public function loadAjax(): bool {
		return true;
	}

	/** {@inheritdoc} */
	public function isUserBlock(): bool {
		return true;
	}

	/** {@inheritdoc} */
	public function isGedcomBlock(): bool {
		return true;
	}

	/**
	 * An HTML form to edit block settings
	 *
	 * @param int $block_id
	 *
	 * @return void
	 */
	public function configureBlock($block_id) {
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

			return;
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

		echo view('blocks/family-tree-statistics-config', [
			'show_last_update'     => $show_last_update,
			'show_common_surnames' => $show_common_surnames,
			'number_of_surnames'   => $number_of_surnames,
			'stat_indi'            => $stat_indi,
			'stat_fam'             => $stat_fam,
			'stat_sour'            => $stat_sour,
			'stat_media'           => $stat_media,
			'stat_repo'            => $stat_repo,
			'stat_surname'         => $stat_surname,
			'stat_events'          => $stat_events,
			'stat_users'           => $stat_users,
			'stat_first_birth'     => $stat_first_birth,
			'stat_last_birth'      => $stat_last_birth,
			'stat_first_death'     => $stat_first_death,
			'stat_last_death'      => $stat_last_death,
			'stat_long_life'       => $stat_long_life,
			'stat_avg_life'        => $stat_avg_life,
			'stat_most_chil'       => $stat_most_chil,
			'stat_avg_chil'        => $stat_avg_chil,
		]);
	}
}
