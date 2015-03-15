<?php namespace Fisharebest\Localization;

/**
 * Class ScriptBatk - Representation of the Batak script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptBatk extends Script {
	/** {@inheritdoc} */
	public function code() {
		return 'Batk';
	}

	/** {@inheritdoc} */
	public function number() {
		return '365';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Batak';
	}
}
