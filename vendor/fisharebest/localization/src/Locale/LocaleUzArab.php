<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Script\ScriptArab;

/**
 * Class LocaleUzArab
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleUzArab extends LocaleUz {
	public function script() {
		return new ScriptArab;
	}

	public function numberSymbols() {
		return array(
			self::GROUP    => self::ARAB_GROUP,
			self::DECIMAL  => self::ARAB_DECIMAL,
			self::NEGATIVE => self::HYPHEN,
		);
	}

	protected function percentFormat() {
		return '%s' . self::ARAB_PERCENT;
	}
}
