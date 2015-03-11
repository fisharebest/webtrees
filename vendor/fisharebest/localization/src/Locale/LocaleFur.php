<?php namespace Fisharebest\Localization;

/**
 * Class LocaleFur - Friulian
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFur extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'furlan';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'FURLAN';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageFur;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::DOT,
			self::DECIMAL => self::COMMA,
		);
	}
}
