<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptTagb - Representation of the Tagbanwa script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptTagb extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Tagb';
	}

	public function number() {
		return '373';
	}

	public function unicodeName() {
		return 'Tagbanwa';
	}
}
