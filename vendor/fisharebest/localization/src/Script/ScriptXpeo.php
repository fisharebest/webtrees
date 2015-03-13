<?php namespace Fisharebest\Localization;

/**
 * Class ScriptXpeo - Representation of the Old Persian script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptXpeo extends Script {
	/** {@inheritdoc} */
	public function code() {
		return 'Xpeo';
	}

	/** {@inheritdoc} */
	public function number() {
		return '030';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Old_Persian';
	}
}
