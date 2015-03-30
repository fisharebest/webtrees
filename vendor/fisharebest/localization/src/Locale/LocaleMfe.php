<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageMfe;

/**
 * Class LocaleMfe - Morisyen
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleMfe extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'kreol morisien';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'KREOL MORISIEN';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageMfe;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP => self::NBSP,
		);
	}
}
