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
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;

/**
 * Class ResearchTaskModule
 */
class ResearchTaskModule extends AbstractModule implements ModuleBlockInterface {
	const DEFAULT_SHOW_OTHER      = '1';
	const DEFAULT_SHOW_UNASSIGNED = '1';
	const DEFAULT_SHOW_FUTURE     = '1';
	const DEFAULT_BLOCK           = '1';

	/** {@inheritdoc} */
	public function getTitle() {
		return /* I18N: Name of a module. Tasks that need further research. */ I18N::translate('Research tasks');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return /* I18N: Description of “Research tasks” module */ I18N::translate('A list of tasks and activities that are linked to the family tree.');
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
		global $ctype, $WT_TREE;

		$show_other      = $this->getBlockSetting($block_id, 'show_other', self::DEFAULT_SHOW_OTHER);
		$show_unassigned = $this->getBlockSetting($block_id, 'show_unassigned', self::DEFAULT_SHOW_UNASSIGNED);
		$show_future     = $this->getBlockSetting($block_id, 'show_future', self::DEFAULT_SHOW_FUTURE);

		extract($cfg, EXTR_OVERWRITE);

		$end_jd = $show_future ? 99999999 : WT_CLIENT_JD;

		$xrefs = Database::prepare(
			"SELECT DISTINCT d_gid FROM `##dates`" .
			" WHERE d_file = :tree_id AND d_fact = '_TODO' AND d_julianday1 < :jd"
		)->execute([
			'tree_id' => $WT_TREE->getTreeId(),
			'jd'      => $end_jd,
		])->fetchOneColumn();

		$records = array_map(function ($xref) use ($WT_TREE) {
			return GedcomRecord::getInstance($xref, $WT_TREE);
		}, $xrefs);

		$tasks = [];

		foreach ($records as $record) {
			foreach ($record->getFacts('_TODO') as $task) {
				$user_name = $task->getAttribute('_WT_USER');

				if ($user_name === Auth::user()->getUserName() || empty($user_name) && $show_unassigned || !empty($user_name) && $show_other) {
					$tasks[] = $task;
				}
			}
		}

		if (empty($records)) {
			$content = '<p>' . I18N::translate('There are no research tasks in this family tree.') . '</p>';
		} else {
			$content = view('blocks/research-tasks', ['tasks' => $tasks]);
		}

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
		return false;
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
			$this->setBlockSetting($block_id, 'show_other', Filter::postBool('show_other'));
			$this->setBlockSetting($block_id, 'show_unassigned', Filter::postBool('show_unassigned'));
			$this->setBlockSetting($block_id, 'show_future', Filter::postBool('show_future'));

			return;
		}

		$show_other      = $this->getBlockSetting($block_id, 'show_other', self::DEFAULT_SHOW_OTHER);
		$show_unassigned = $this->getBlockSetting($block_id, 'show_unassigned', self::DEFAULT_SHOW_UNASSIGNED);
		$show_future     = $this->getBlockSetting($block_id, 'show_future', self::DEFAULT_SHOW_FUTURE);

		echo view('blocks/research-tasks-config', [
			'show_future'     => $show_future,
			'show_other'      => $show_other,
			'show_unassigned' => $show_unassigned,
		]);

	}
}
