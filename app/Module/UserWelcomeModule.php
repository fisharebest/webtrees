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
use Fisharebest\Webtrees\Html;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Module;

/**
 * Class UserWelcomeModule
 */
class UserWelcomeModule extends AbstractModule implements ModuleBlockInterface {
	/** {@inheritdoc} */
	public function getTitle() {
		return /* I18N: Name of a module */
			I18N::translate('My page');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return /* I18N: Description of the “My page” module */
			I18N::translate('A greeting message and useful links for a user.');
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
		global $WT_TREE;

		$gedcomid   = $WT_TREE->getUserPreference(Auth::user(), 'gedcomid');
		$individual = Individual::getInstance($gedcomid, $WT_TREE);
		$links      = [];

		if ($individual) {
			if (Module::isActiveChart($WT_TREE, 'pedigree_chart')) {
				$links[] = [
					'url'   => route('pedigree', ['xref' => $individual->getXref(), 'ged' => $individual->getTree()->getName()]),
					'title' => I18N::translate('Default chart'),
					'icon'  => 'icon-pedigree',
				];
			}

			$links[] = [
				'url'   => $individual->url(),
				'title' => I18N::translate('My individual record'),
				'icon'  => 'icon-indis',
			];
		}

		$links[] = [
			'url'   => route('my-account', []),
			'title' => I18N::translate('My account'),
			'icon'  => 'icon-mypage',
		];
		$content = view('blocks/welcome', ['links' => $links]);

		if ($template) {
			return view('blocks/template', [
				'block'      => str_replace('_', '-', $this->getName()),
				'id'         => $block_id,
				'config_url' => '',
				'title'      => /* I18N: A %s is the user’s name */ I18N::translate('Welcome %s', Auth::user()->getRealName()),
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
		return false;
	}

	/**
	 * An HTML form to edit block settings
	 *
	 * @param int $block_id
	 *
	 * @return void
	 */
	public function configureBlock($block_id) {
	}
}
