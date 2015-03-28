<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageMgh;

/**
 * Class LocaleMgh - Makhuwa-Meetto
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleMgh extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Makua';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'MAKUA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageMgh;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::DOT,
			self::DECIMAL => self::COMMA,
		);
	}
}
