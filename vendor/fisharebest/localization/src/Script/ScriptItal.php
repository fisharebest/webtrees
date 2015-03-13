<?php namespace Fisharebest\Localization;

/**
 * Class ScriptItal - Representation of the Old Italic (Etruscan, Oscan, etc.) script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptItal extends Script {
	/** {@inheritdoc} */
	public function code() {
		return 'Ital';
	}

	/** {@inheritdoc} */
	public function number() {
		return '210';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Old_Italic';
	}
}
