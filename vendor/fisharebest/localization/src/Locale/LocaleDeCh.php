<?php namespace Fisharebest\Localization;

/**
 * Class LocaleDeCh - Swiss High German
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleDeCh extends LocaleDe {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Schweizer Hochdeutsch';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'SCHWEIZER HOCHDEUTSCH';
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::PRIME,
			self::DECIMAL => self::DOT,
		);
	}

	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryCh;
	}
}
