<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageLb;

/**
 * Class LocaleLb - Luxembourgish
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleLb extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Lëtzebuergesch';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'LETZEBUERGESCH';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageLb;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::DOT,
			self::DECIMAL => self::COMMA,
		);
	}

	/** {@inheritdoc} */
	protected function percentFormat() {
		return '%s' . self::NBSP . self::PERCENT;
	}
}
