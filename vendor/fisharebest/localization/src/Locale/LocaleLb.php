<?php namespace Fisharebest\Localization;

/**
 * Class LocaleLb - Luxembourgish
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleLb extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Lëtzebuergesch';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
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
