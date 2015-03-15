<?php namespace Fisharebest\Localization;

/**
 * Class ScriptShaw - Representation of the Shavian (Shaw) script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptShaw extends Script {
	/** {@inheritdoc} */
	public function code() {
		return 'Shaw';
	}

	/** {@inheritdoc} */
	public function number() {
		return '281';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Shavian';
	}
}
