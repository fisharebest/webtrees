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
	/** {@inheritdoc} */
	public function endonym() {
		return 'پښتو';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguagePs;
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
