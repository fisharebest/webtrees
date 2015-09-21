<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptTalu - Representation of the New Tai Lue script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptTalu extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Talu';
	}

	/** {@inheritdoc} */
	public function numerals() {
		return array('᧐', '᧑', '᧒', '᧓', '᧔', '᧕', '᧖', '᧗', '᧘', '᧙');
	}

	/** {@inheritdoc} */
	public function number() {
		return '354';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'New_Tai_Lue';
	}
}
