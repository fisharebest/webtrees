<?php namespace Fisharebest\Localization;

/**
 * Class Script - Representation of a locale variant.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
abstract class Variant {
	/**
	 * The code for this variant.
	 *
	 * @return string
	 */
	abstract public function code();
}
