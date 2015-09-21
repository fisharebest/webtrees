<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptCakm - Representation of the Chakma script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptCakm extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Cakm';
	}

	/** {@inheritdoc} */
	public function numerals() {
		return array('𑄶', '𑄷', '𑄸', '𑄹', '𑄺', '𑄻', '𑄼', '𑄽', '𑄾', '𑄿');
	}

	/** {@inheritdoc} */
	public function number() {
		return '349';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Chakma';
	}
}
