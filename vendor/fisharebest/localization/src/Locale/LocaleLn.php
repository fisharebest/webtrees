<?php namespace Fisharebest\Localization;

/**
 * Class LocaleLn - Lingala
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleLn extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'lingála';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'LINGALA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageLn;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::DOT,
			self::DECIMAL => self::COMMA,
		);
	}
}
