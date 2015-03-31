<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageTn;

/**
 * Class LocaleTn - Tswana
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleTn extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Setswana';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'SETSWANA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageTn;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP => self::NBSP,
		);
	}
}
