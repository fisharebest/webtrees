<?php namespace Fisharebest\Localization;

/**
 * Class LocaleBez - Bena
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleBez extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Hibena';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'HIBENA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageBez;
	}
}
