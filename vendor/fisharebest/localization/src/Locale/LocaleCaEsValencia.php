<?php namespace Fisharebest\Localization;

/**
 * Class LocaleCaEsValencia
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleCaEsValencia extends LocaleCaEs {
	/** {@inheritdoc} */
	public function variant() {
		return new VariantValencia();
	}
}
