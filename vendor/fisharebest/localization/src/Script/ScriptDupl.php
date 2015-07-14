<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptDupl - Representation of the Duployan shorthand, Duployan stenography script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptDupl extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Dupl';
	}

	/** {@inheritdoc} */
	public function number() {
		return '755';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Duployan';
	}
}
