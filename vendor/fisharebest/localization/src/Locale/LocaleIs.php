<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageIs;

/**
 * Class LocaleIs - Icelandic
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleIs extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function collation() {
		return 'icelandic_ci';
	}

	/** {@inheritdoc} */
	public function endonym() {
		return 'íslenska';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'ISLENSKA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageIs;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::DOT,
			self::DECIMAL => self::COMMA,
		);
	}
}
