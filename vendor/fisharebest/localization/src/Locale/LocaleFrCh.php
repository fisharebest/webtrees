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
	/** {@inheritdoc} */
	public function endonym() {
		return 'français suisse';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'FRANCAIS SUISSE';
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::NBSP,
			self::DECIMAL => self::DOT,
		);
	}

	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryCh;
	}
}
