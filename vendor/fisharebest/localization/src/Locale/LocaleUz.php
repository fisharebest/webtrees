<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageUz;

/**
 * Class LocaleUz - Uzbek
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleUz extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'oʻzbekcha';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'OZBEKCHA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageUz;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::NBSP,
			self::DECIMAL => self::COMMA,
		);
	}
}
