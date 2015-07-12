<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptMani - Representation of the Manichaean script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptMani extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Mani';
	}

	/** {@inheritdoc} */
	public function number() {
		return '139';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Manichaean';
	}
}
