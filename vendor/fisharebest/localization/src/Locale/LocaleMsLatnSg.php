<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritorySg;

/**
 * Class LocaleMsLatnSg
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleMsLatnSg extends LocaleMsLatn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritorySg;
	}
}
