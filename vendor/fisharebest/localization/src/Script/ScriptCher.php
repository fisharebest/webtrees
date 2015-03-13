<?php namespace Fisharebest\Localization;

/**
 * Class ScriptCher - Representation of the Cherokee script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptCher extends Script {
	/** {@inheritdoc} */
	public function code() {
		return 'Cher';
	}

	/** {@inheritdoc} */
	public function number() {
		return '445';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Cherokee';
	}
}
