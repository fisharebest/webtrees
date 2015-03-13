<?php namespace Fisharebest\Localization;

/**
 * Class LocaleCa - Catalan
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleCa extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'català';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'CATALA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageCa;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::DOT,
			self::DECIMAL => self::COMMA,
		);
	}
}
