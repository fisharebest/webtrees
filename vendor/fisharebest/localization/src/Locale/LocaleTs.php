<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageTs;

/**
 * Class LocaleTs - Tsonga
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleTs extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Xitsonga';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'XITSONGA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageTs;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::NBSP,
			self::DECIMAL => self::COMMA,
		);
	}
}
