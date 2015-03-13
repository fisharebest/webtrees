<?php namespace Fisharebest\Localization;

/**
 * Class LocaleYav - Yangben
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleYav extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'nuasue';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'NUASUE';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageYav;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::NBSP,
			self::DECIMAL => self::COMMA,
		);
	}
}
