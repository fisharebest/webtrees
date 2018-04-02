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
use Fisharebest\Webtrees\Module;
use Fisharebest\Webtrees\Site;

/**
 * Class WelcomeBlockModule
 */
class WelcomeBlockModule extends AbstractModule implements ModuleBlockInterface {
	/** {@inheritdoc} */
	public function getTitle() {
		return /* I18N: Name of a module */
			I18N::translate('Home page');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return /* I18N: Description of the “Home page” module */
			I18N::translate('A greeting message for site visitors.');
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
		global $controller;

		$individual = $controller->getSignificantIndividual();

		$links = [];

		if (Module::isActiveChart($individual->getTree(), 'pedigree_chart')) {
			$links[] = [
				'url'   => route('pedigree', ['xref' => $individual->getXref(), 'ged' => $individual->getTree()->getName()]),
				'title' => I18N::translate('Default chart'),
				'icon'  => 'icon-pedigree',
			];
		}

		$links[] = [
			'url'   => $individual->url(),
			'title' => I18N::translate('Default individual'),
			'icon'  => 'icon-indis',
		];

		if (Site::getPreference('USE_REGISTRATION_MODULE') === '1' && !Auth::check()) {
			$links[] = [
				'url'   => route('register'),
				'title' => I18N::translate('Request a new user account'),
				'icon'  => 'icon-user_add',
			];
		}

		$content = view('blocks/welcome', ['links' => $links]);

		if ($template) {
			return view('blocks/template', [
				'block'      => str_replace('_', '-', $this->getName()),
				'id'         => $block_id,
				'config_url' => '',
				'title'      => $individual->getTree()->getTitle(),
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
		return false;
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
	}
}
