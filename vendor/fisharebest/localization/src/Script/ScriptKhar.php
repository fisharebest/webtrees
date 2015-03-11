<?php namespace Fisharebest\Localization;

/**
 * Class ScriptKhar - Representation of the Kharoshthi script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptKhar extends Script {
	/** {@inheritdoc} */
	public function code() {
		return 'Khar';
	}

	/** {@inheritdoc} */
	public function number() {
		return '305';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Kharoshthi';
	}
}
