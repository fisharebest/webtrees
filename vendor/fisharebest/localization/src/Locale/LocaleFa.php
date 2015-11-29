<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageFa;

/**
 * Class LocaleFa - Persian
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFa extends AbstractLocale implements LocaleInterface {
	public function collation() {
		return 'persian_ci';
	}

	public function endonym() {
		return 'فارسی';
	}

	public function language() {
		return new LanguageFa;
	}

	public function numerals() {
		return array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');
	}

	public function numberSymbols() {
		return array(
			self::GROUP    => self::ARAB_GROUP,
			self::DECIMAL  => self::ARAB_DECIMAL,
			self::NEGATIVE => self::LTR_MARK . self::MINUS_SIGN,
		);
	}

	protected function percentFormat() {
		return '%s' . self::ARAB_PERCENT;
	}
}
