<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageSg;

/**
 * Class LocaleSg - Sango
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSg extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Sängö';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'SANGO';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageSg;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::DOT,
			self::DECIMAL => self::COMMA,
		);
	}
}
