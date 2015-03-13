<?php namespace Fisharebest\Localization;

/**
 * Class LocaleLnCf
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleLnCf extends LocaleLn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryCf;
	}
}
