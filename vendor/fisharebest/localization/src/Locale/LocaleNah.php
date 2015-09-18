<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageNah;

/**
 * Class LocaleNah - Nahuatl
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleNah extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Nahuatlahtolli';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'NAHUATLAHTOLLI';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageNah;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::COMMA,
			self::DECIMAL => self::DOT,
		);
	}

	/** {@inheritdoc} */
	protected function percentFormat() {
		return '%s' . self::PERCENT;
	}

}
