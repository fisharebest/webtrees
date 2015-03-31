<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryMx;

/**
 * Class LocaleEsMx - Mexican Spanish
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEsMx extends LocaleEs {
	/** {@inheritdoc} */
	public function endonym() {
		return 'español de México';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'ESPANOL DE MEXICO';
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::COMMA,
			self::DECIMAL => self::DOT,
		);
	}

	/** {@inheritdoc} */
	protected function percentFormat() {
		return '%s' . self::PERCENT;
	}

	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryMx;
	}
}
