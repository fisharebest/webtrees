<?php namespace Fisharebest\Localization;

/**
 * Class LocaleWae - Walser
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleWae extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Walser';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'WALSER';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageWae;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::APOSTROPHE,
			self::DECIMAL => self::COMMA,
		);
	}
}
