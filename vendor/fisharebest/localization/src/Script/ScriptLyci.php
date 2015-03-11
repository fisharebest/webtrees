<?php namespace Fisharebest\Localization;

/**
 * Class ScriptLyci - Representation of the Lycian script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptLyci extends Script {
	/** {@inheritdoc} */
	public function code() {
		return 'Lyci';
	}

	/** {@inheritdoc} */
	public function number() {
		return '202';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Lycian';
	}
}
