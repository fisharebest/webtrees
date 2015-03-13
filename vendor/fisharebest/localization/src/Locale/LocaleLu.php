<?php namespace Fisharebest\Localization;

/**
 * Class LocaleLu - Luba-Katanga
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleLu extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Tshiluba';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
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
