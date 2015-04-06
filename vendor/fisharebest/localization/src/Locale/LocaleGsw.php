<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageGsw;

/**
 * Class LocaleGsw - Swiss German
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleGsw extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Schwiizertüütsch';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'SCHWIIZERTUUTSCH';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageGsw;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP    => self::APOSTROPHE,
			self::NEGATIVE => self::MINUS_SIGN,
		);
	}

	/** {@inheritdoc} */
	protected function percentFormat() {
		return '%s' . self::NBSP . self::PERCENT;
	}
}
