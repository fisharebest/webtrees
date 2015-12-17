<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Variant\VariantValencia;

/**
 * Class LocaleCaEsValencia
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleCaEsValencia extends LocaleCaEs {
	public function variant() {
		return new VariantValencia();
	}
}
