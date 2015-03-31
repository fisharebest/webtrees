<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageEwo;

/**
 * Class LocaleEwo - Ewondo
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEwo extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'ewondo';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'EWONDO';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageEwo;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::NBSP,
			self::DECIMAL => self::COMMA,
		);
	}
}
