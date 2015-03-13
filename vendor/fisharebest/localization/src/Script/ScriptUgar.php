<?php namespace Fisharebest\Localization;

/**
 * Class ScriptUgar - Representation of the Ugaritic script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptUgar extends Script {
	/** {@inheritdoc} */
	public function code() {
		return 'Ugar';
	}

	/** {@inheritdoc} */
	public function number() {
		return '040';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Ugaritic';
	}
}
