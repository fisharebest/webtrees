<?php namespace Fisharebest\Localization;

/**
 * Class LocaleSr - Serbian
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSr extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'српски';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'СРПСКИ';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageSr;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::DOT,
			self::DECIMAL => self::COMMA,
		);
	}
}
