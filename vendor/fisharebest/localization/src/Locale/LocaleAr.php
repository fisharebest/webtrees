<?php namespace Fisharebest\Localization;

/**
 * Class LocaleAr - Arabic
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleAr extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'العربية';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageAr;
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
