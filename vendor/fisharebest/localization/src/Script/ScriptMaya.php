<?php namespace Fisharebest\Localization;

/**
 * Class ScriptMaya - Representation of the Mayan hieroglyphs script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptMaya extends Script {
	/** {@inheritdoc} */
	public function code() {
		return 'Maya';
	}

	/** {@inheritdoc} */
	public function number() {
		return '090';
	}
}
