<?php namespace Fisharebest\Localization;

/**
 * Class ScriptLatn - Representation of the Latin script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptLatn extends Script {
	/** {@inheritdoc} */
	public function code() {
		return 'Latn';
	}

	/** {@inheritdoc} */
	public function number() {
		return '215';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Latin';
	}
}
