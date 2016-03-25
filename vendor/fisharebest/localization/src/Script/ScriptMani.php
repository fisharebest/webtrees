<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptMani - Representation of the Manichaean script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptMani extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Mani';
	}

	public function number() {
		return '139';
	}

	public function unicodeName() {
		return 'Manichaean';
	}
}
