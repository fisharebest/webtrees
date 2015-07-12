<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\Territory419;

/**
 * Class LocaleEs419 - Latin American Spanish
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEs419 extends LocaleEs {
	/** {@inheritdoc} */
	public function endonym() {
		return 'español latinoamericano';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'ESPANOL LATINOAMERICANO';
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::COMMA,
			self::DECIMAL => self::DOT,
		);
	}

	/** {@inheritdoc} */
	public function territory() {
		return new Territory419;
	}
}
