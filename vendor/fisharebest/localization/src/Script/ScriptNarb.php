<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptNarb - Representation of the Old North Arabian (Ancient North Arabian) script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptNarb extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Narb';
	}

	/** {@inheritdoc} */
	public function number() {
		return '106';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Old_North_Arabian';
	}
}
