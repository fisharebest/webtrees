<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptLyci - Representation of the Lycian script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptLyci extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Lyci';
	}

	public function number() {
		return '202';
	}

	public function unicodeName() {
		return 'Lycian';
	}
}
