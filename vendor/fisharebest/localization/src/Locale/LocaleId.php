<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageId;

/**
 * Class LocaleId - Indonesian
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleId extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Bahasa Indonesia';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'BAHASA INDONESIA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageId;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::DOT,
			self::DECIMAL => self::COMMA,
		);
	}
}
