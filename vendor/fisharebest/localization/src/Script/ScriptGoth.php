<?php namespace Fisharebest\Localization;

/**
 * Class ScriptGoth - Representation of the Gothic script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptGoth extends Script {
	/** {@inheritdoc} */
	public function code() {
		return 'Goth';
	}

	/** {@inheritdoc} */
	public function number() {
		return '206';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Gothic';
	}
}
