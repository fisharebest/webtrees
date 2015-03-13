<?php namespace Fisharebest\Localization;

/**
 * Class LocaleFfCm
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFfCm extends LocaleFf {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryCm;
	}
}
