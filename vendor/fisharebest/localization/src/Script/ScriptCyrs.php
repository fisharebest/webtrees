<?php namespace Fisharebest\Localization;

/**
 * Class ScriptCyrs - Representation of the Cyrillic (Old Church Slavonic variant) script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptCyrs extends Script {
	/** {@inheritdoc} */
	public function code() {
		return 'Cyrs';
	}

	/** {@inheritdoc} */
	public function number() {
		return '221';
	}
}
