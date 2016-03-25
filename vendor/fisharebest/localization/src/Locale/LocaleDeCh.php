<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryCh;

/**
 * Class LocaleDeCh - Swiss High German
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleDeCh extends LocaleDe {
	public function endonym() {
		return 'Schweizer Hochdeutsch';
	}

	public function endonymSortable() {
		return 'SCHWEIZER HOCHDEUTSCH';
	}

	public function numberSymbols() {
		return array(
			self::GROUP   => self::PRIME,
			self::DECIMAL => self::DOT,
		);
	}

	protected function percentFormat() {
		return '%s%%';
	}

	public function territory() {
		return new TerritoryCh;
	}
}
