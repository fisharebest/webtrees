<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageMzn;

/**
 * Class LocaleLrc - Mazanderani
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleMzn extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'مازرونی';
	}

	public function language() {
		return new LanguageMzn;
	}

	public function numerals() {
		return array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');
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
