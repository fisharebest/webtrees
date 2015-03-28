<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageWae;

/**
 * Class LocaleWae - Walser
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleWae extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Walser';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
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
