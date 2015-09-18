<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptLana - Representation of the Tai Tham (Lanna) script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptLana extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Lana';
	}

	/** {@inheritdoc} */
	public function numerals() {
		return array('᪀', '᪁', '᪂', '᪃', '᪄', '᪅', '᪆', '᪇', '᪈', '᪉');
	}

	/** {@inheritdoc} */
	public function number() {
		return '351';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Tai_Tham';
	}
}
