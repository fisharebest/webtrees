<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptLaoo - Representation of the Lao script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptLaoo extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Laoo';
	}

	/** {@inheritdoc} */
	public function numerals() {
		return array('໐', '໑', '໒', '໓', '໔', '໕', '໖', '໗', '໘', '໙');
	}

	/** {@inheritdoc} */
	public function number() {
		return '356';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Lao';
	}
}
