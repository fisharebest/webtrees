<?php namespace Fisharebest\Localization;

/**
 * Class ScriptGrek - Representation of the Greek script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptGrek extends Script {
	/** {@inheritdoc} */
	public function code() {
		return 'Grek';
	}

	/** {@inheritdoc} */
	public function number() {
		return '200';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Greek';
	}
}
