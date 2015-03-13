<?php namespace Fisharebest\Localization;

/**
 * Class ScriptLinb - Representation of the Linear B script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptLinb extends Script {
	/** {@inheritdoc} */
	public function code() {
		return 'Linb';
	}

	/** {@inheritdoc} */
	public function number() {
		return '401';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Linear_B';
	}
}
