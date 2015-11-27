<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageLrc;

/**
 * Class LocaleLrc - Luri
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleLrc extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'لۊری شومالی';
	}

	public function language() {
		return new LanguageLrc;
	}

	public function numberSymbols() {
		return array(
				self::GROUP    => self::ARAB_GROUP,
				self::DECIMAL  => self::ARAB_DECIMAL,
				self::NEGATIVE => self::LTR_MARK . self::HYPHEN . self::LTR_MARK,
		);
	}

	protected function percentFormat() {
		return '%s' . self::ARAB_PERCENT;
	}
}
