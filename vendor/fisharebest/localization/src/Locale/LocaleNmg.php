<?php namespace Fisharebest\Localization;

/**
 * Class LocaleNmg - Kwasio
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleNmg extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Kwasio';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'KWASIO';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageNmg;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::NBSP,
			self::DECIMAL => self::COMMA,
		);
	}
}
