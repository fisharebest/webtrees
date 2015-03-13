<?php namespace Fisharebest\Localization;

/**
 * Class LocaleSq - Albanian
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSq extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'shqip';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'SHQIP';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageSq;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::NBSP,
			self::DECIMAL => self::COMMA,
		);
	}
}
