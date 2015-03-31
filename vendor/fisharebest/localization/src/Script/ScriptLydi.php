<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptLydi - Representation of the Lydian script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptLydi extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Lydi';
	}

	/** {@inheritdoc} */
	public function number() {
		return '116';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Lydian';
	}
}
