<?php namespace Fisharebest\Localization;

/**
 * Class ScriptKana - Representation of the Katakana script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptKana extends Script {
	/** {@inheritdoc} */
	public function code() {
		return 'Kana';
	}

	/** {@inheritdoc} */
	public function number() {
		return '411';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Katakana';
	}
}
