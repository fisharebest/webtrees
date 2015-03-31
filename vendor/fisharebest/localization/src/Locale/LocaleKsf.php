<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageKsf;

/**
 * Class LocaleKsf - Bafia
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleKsf extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'rikpa';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'RIKPA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageKsf;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::NBSP,
			self::DECIMAL => self::COMMA,
		);
	}
}
