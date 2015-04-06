<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageEo;

/**
 * Class LocaleEo - Esperanto
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEo extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function collation() {
		return 'esperanto_ci';
	}

	/** {@inheritdoc} */
	public function endonym() {
		return 'esperanto';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'ESPERANTO';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageEo;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP    => self::NBSP,
			self::DECIMAL  => self::COMMA,
			self::NEGATIVE => self::MINUS_SIGN,
		);
	}
}
