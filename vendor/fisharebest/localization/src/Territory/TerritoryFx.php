<?php namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory FX - Metropolitan France.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryFx extends AbstractTerritory implements TerritoryInterface {
	public function code() {
		return 'FX';
	}
}
