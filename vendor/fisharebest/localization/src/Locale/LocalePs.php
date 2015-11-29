<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguagePs;

/**
 * Class LocalePs - Pashto
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocalePs extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'پښتو';
	}

	public function language() {
		return new LanguagePs;
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
