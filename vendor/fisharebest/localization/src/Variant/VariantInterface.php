<?php namespace Fisharebest\Localization\Variant;

/**
 * Interface VariantInterface - Representation of a locale variant.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
interface VariantInterface {
	/**
	 * The code for this variant.
	 *
	 * @return string
	 */
	public function code();
}
