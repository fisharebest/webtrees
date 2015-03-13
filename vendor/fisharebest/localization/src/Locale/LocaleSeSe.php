<?php namespace Fisharebest\Localization;

/**
 * Class LocaleSeSe
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSeSe extends LocaleSe {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritorySe;
	}
}
