<?php namespace Fisharebest\Localization;

/**
 * Class LocaleKs - Kashmiri
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleKs extends Locale {
	/** {@inheritdoc} */
	protected function digitsGroup() {
		return 2;
	}

	/** {@inheritdoc} */
	public function endonym() {
		return 'کٲشُر';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageKs;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP    => self::ARAB_GROUP,
			self::DECIMAL  => self::ARAB_DECIMAL,
			self::NEGATIVE => self::LTR_MARK . self::HYPHEN . self::LTR_MARK,
		);
	}

	/** {@inheritdoc} */
	protected function percentFormat() {
		return '%s' . self::ARAB_PERCENT;
	}
}
