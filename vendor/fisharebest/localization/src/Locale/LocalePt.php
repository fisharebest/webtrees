<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguagePt;

/**
 * Class LocalePt - Portuguese
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocalePt extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'português';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'PORTUGUES';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguagePt;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::DOT,
			self::DECIMAL => self::COMMA,
		);
	}
}
