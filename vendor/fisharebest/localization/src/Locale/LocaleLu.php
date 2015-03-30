<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageLu;

/**
 * Class LocaleLu - Luba-Katanga
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleLu extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Tshiluba';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'TSHILUBA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageLu;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::DOT,
			self::DECIMAL => self::COMMA,
		);
	}
}
