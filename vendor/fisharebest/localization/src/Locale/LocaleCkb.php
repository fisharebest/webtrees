<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageCkb;

/**
 * Class LocaleCkb - Sorani
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleCkb extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'سۆرانی';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageCkb;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP    => self::ARAB_GROUP,
			self::DECIMAL  => self::ARAB_DECIMAL,
			self::NEGATIVE => self::RTL_MARK . self::HYPHEN,
		);
	}

	/** {@inheritdoc} */
	protected function percentFormat() {
		return '%s' . self::ARAB_PERCENT;
	}
}
