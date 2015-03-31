<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageAz;

/**
 * Class LocaleAz - Azerbaijani
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleAz extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'azərbaycan';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'AZERBAYCAN';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageAz;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::DOT,
			self::DECIMAL => self::COMMA,
		);
	}
}
