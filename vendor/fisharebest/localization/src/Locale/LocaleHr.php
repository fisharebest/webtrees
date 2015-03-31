<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageHr;

/**
 * Class LocaleHr - Croatian
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleHr extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function collation() {
		return 'croatian_ci';
	}

	/** {@inheritdoc} */
	public function endonym() {
		return 'hrvatski';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'HRVATSKI';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageHr;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::DOT,
			self::DECIMAL => self::COMMA,
		);
	}
}
