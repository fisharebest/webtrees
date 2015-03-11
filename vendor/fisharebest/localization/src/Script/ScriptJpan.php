<?php namespace Fisharebest\Localization;

/**
 * Class ScriptJpan - Representation of the Japanese script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptJpan extends Script {
	/** {@inheritdoc} */
	public function code() {
		return 'Jpan';
	}

	/** {@inheritdoc} */
	public function number() {
		return '413';
	}
}
