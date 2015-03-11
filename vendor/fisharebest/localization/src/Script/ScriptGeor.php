<?php namespace Fisharebest\Localization;

/**
 * Class ScriptGeor - Representation of the Georgian script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptGeor extends Script {
	/** {@inheritdoc} */
	public function code() {
		return 'Geor';
	}

	/** {@inheritdoc} */
	public function number() {
		return '240';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Georgian';
	}
}
