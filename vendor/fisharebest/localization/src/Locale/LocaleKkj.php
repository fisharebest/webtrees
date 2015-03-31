<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageKkj;

/**
 * Class LocaleKkj - Kako
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleKkj extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'kakɔ';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'KAKO';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageKkj;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::DOT,
			self::DECIMAL => self::COMMA,
		);
	}
}
