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
use Fisharebest\Webtrees\Functions\FunctionsDate;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Stats;
use Fisharebest\Webtrees\Tree;

/**
 * Class HtmlBlockModule
 */
class HtmlBlockModule extends AbstractModule implements ModuleBlockInterface {
	/** {@inheritdoc} */
	public function getTitle() {
		return /* I18N: Name of a module */
			I18N::translate('HTML');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return /* I18N: Description of the “HTML” module */
			I18N::translate('Add your own text and graphics.');
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

		$title          = $this->getBlockSetting($block_id, 'title', '');
		$content        = $this->getBlockSetting($block_id, 'html', '');
		$gedcom         = $this->getBlockSetting($block_id, 'gedcom');
		$show_timestamp = $this->getBlockSetting($block_id, 'show_timestamp', '0');
		$languages      = $this->getBlockSetting($block_id, 'languages');

		// Only show this block for certain languages
		if ($languages && !in_array(WT_LOCALE, explode(',', $languages))) {
			return '';
		}

		/*
		 * Select GEDCOM
		 */
		switch ($gedcom) {
			case '__current__':
				$stats = new Stats($WT_TREE);
				break;
			case '__default__':
				$tree = Tree::findByName(Site::getPreference('DEFAULT_GEDCOM'));
				if ($tree) {
					$stats = new Stats($tree);
				} else {
					$stats = new Stats($WT_TREE);
				}
				break;
			default:
				$tree = Tree::findByName($gedcom);
				if ($tree) {
					$stats = new Stats($tree);
				} else {
					$stats = new Stats($WT_TREE);
				}
				break;
		}

		/*
		* Retrieve text, process embedded variables
		*/
		$title   = $stats->embedTags($title);
		$content = $stats->embedTags($content);

		if ($show_timestamp === '1') {
			$content .= '<br>' . FunctionsDate::formatTimestamp($this->getBlockSetting($block_id, 'timestamp', WT_TIMESTAMP) + WT_TIMESTAMP_OFFSET);
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
				'title'      => $title,
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
			$languages = Filter::postArray('lang');
			$this->setBlockSetting($block_id, 'gedcom', Filter::post('gedcom'));
			$this->setBlockSetting($block_id, 'title', Filter::post('title'));
			$this->setBlockSetting($block_id, 'html', Filter::post('html'));
			$this->setBlockSetting($block_id, 'show_timestamp', Filter::postBool('show_timestamp'));
			$this->setBlockSetting($block_id, 'timestamp', Filter::post('timestamp'));
			$this->setBlockSetting($block_id, 'languages', implode(',', $languages));

			return;
		}

		$templates = [
			I18N::translate('Keyword examples')      => view('blocks/html-template-keywords', []),
			I18N::translate('Narrative description') => view('blocks/html-template-narrative', []),
			I18N::translate('Statistics')            => view('blocks/html-template-statistics', []),
		];

		$title          = $this->getBlockSetting($block_id, 'title', '');
		$html           = $this->getBlockSetting($block_id, 'html', '');
		$gedcom         = $this->getBlockSetting($block_id, 'gedcom', '__current__');
		$show_timestamp = $this->getBlockSetting($block_id, 'show_timestamp', '0');
		$languages      = explode(',', $this->getBlockSetting($block_id, 'languages'));
		$all_trees      = Tree::getNameList();

		echo view('blocks/html-config', [
			'all_trees'       => $all_trees,
			'enable_ckeditor' => Module::getModuleByName('ckeditor'),
			'gedcom'          => $gedcom,
			'html'            => $html,
			'languages'       => $languages,
			'show_timestamp'  => $show_timestamp,
			'templates'       => $templates,
			'title'           => $title,
		]);
	}
}
