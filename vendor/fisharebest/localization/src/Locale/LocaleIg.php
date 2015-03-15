<?php namespace Fisharebest\Localization;

/**
 * Class LocaleIg - Igbo
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleIg extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Igbo';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'IGBO';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageIg;
	}
}
