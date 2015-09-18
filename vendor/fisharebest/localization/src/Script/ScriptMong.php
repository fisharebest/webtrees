<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptMong - Representation of the Mongolian script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptMong extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Mong';
	}

	/** {@inheritdoc} */
	public function numerals() {
		return array('᠐', '᠑', '᠒', '᠓', '᠔', '᠕', '᠖', '᠗', '᠘', '᠙');
	}

	/** {@inheritdoc} */
	public function number() {
		return '145';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Mongolian';
	}
}
