<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageSl;

/**
 * Class LocaleSl - Slovenian
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSl extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function collation() {
		return 'slovenian_ci';
	}

	/** {@inheritdoc} */
	public function endonym() {
		return 'slovenščina';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'SLOVENSCINA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageSl;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::DOT,
			self::DECIMAL => self::COMMA,
		);
	}
}
