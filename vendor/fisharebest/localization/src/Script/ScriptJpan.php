<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptJpan - Representation of the Japanese script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptJpan extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Jpan';
	}

	public function number() {
		return '413';
	}
}
