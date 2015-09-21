<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptJava - Representation of the Javanese script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptJava extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Java';
	}

	/** {@inheritdoc} */
	public function numerals() {
		return array('꧐', '꧑', '꧒', '꧓', '꧔', '꧕', '꧖', '꧗', '꧘', '꧙');
	}

	/** {@inheritdoc} */
	public function number() {
		return '361';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Javanese';
	}
}
