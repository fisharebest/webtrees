<?php namespace Fisharebest\Localization;

/**
 * Class Territory - Representation of the territory TW - Taiwan, Province of China.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryTw extends Territory {
	/** {@inheritdoc} */
	public function code() {
		return 'TW';
	}

	/** {@inheritdoc} */
	public function firstDay() {
		return 0;
	}
}
