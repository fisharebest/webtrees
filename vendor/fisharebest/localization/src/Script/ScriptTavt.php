<?php namespace Fisharebest\Localization;

/**
 * Class ScriptTavt - Representation of the Tai Viet script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptTavt extends Script {
	/** {@inheritdoc} */
	public function code() {
		return 'Tavt';
	}

	/** {@inheritdoc} */
	public function number() {
		return '359';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Tai_Viet';
	}
}
