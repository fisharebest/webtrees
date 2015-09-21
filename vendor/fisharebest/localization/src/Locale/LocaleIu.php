<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageIu;

/**
 * Class LocaleIu - Inuktitut
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleIu extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'ᐃᓄᒃᑎᑐᑦ';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'ᐃᓄᒃᑎᑐᑦ';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageIu;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::NBSP,
			self::DECIMAL => self::COMMA,
		);
	}

	/** {@inheritdoc} */
	protected function percentFormat() {
		return '%s' . self::NBSP . self::PERCENT;
	}
}
