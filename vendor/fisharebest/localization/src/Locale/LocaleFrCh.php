<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryCh;

/**
 * Class LocaleFrCh - Swiss French
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFrCh extends LocaleFr {
	public function endonym() {
		return 'français suisse';
	}

	public function endonymSortable() {
		return 'FRANCAIS SUISSE';
	}

	public function numberSymbols() {
		return array(
			self::GROUP   => self::NBSP,
			self::DECIMAL => self::DOT,
		);
	}

	protected function percentFormat() {
		return '%s' . self::PERCENT;
	}

	public function territory() {
		return new TerritoryCh;
	}
}
