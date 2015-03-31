<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageNr;

/**
 * Class LocaleNr - South Ndebele
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleNr extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'isiNdebele';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'ISINDEBELE';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageNr;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::NBSP,
			self::DECIMAL => self::COMMA,
		);
	}
}
