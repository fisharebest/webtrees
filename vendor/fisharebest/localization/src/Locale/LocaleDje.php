<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageDje;

/**
 * Class LocaleDje - Zarma
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleDje extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Zarmaciine';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'ZARMACIINE';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageDje;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP => self::NBSP,
		);
	}
}
