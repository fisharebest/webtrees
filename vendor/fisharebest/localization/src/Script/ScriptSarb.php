<?php namespace Fisharebest\Localization;

/**
 * Class ScriptSarb - Representation of the Old South Arabian script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptSarb extends Script {
	/** {@inheritdoc} */
	public function code() {
		return 'Sarb';
	}

	/** {@inheritdoc} */
	public function number() {
		return '105';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Old_South_Arabian';
	}
}
