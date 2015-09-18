<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptTaml - Representation of the Tamil script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptTaml extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Taml';
	}

	/** {@inheritdoc} */
	public function numerals() {
		return array('௦', '௧', '௨', '௩', '௪', '௫', '௬', '௭', '௮', '௯');
	}

	/** {@inheritdoc} */
	public function number() {
		return '346';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Tamil';
	}
}
