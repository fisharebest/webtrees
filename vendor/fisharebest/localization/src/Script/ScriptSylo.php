<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptSylo - Representation of the Syloti Nagri script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptSylo extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Sylo';
	}

	/** {@inheritdoc} */
	public function number() {
		return '316';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Syloti_Nagri';
	}
}
