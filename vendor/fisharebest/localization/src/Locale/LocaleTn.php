<?php namespace Fisharebest\Localization;

/**
 * Class LocaleTn - Tswana
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleTn extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Setswana';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'SETSWANA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageTn;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP => self::NBSP,
		);
	}
}
