<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptLana - Representation of the Tai Tham (Lanna) script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptLana extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Lana';
	}

	public function numerals() {
		return array('᪀', '᪁', '᪂', '᪃', '᪄', '᪅', '᪆', '᪇', '᪈', '᪉');
	}

	public function number() {
		return '351';
	}

	public function unicodeName() {
		return 'Tai_Tham';
	}
}
