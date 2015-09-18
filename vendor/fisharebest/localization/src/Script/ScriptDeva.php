<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptDeva - Representation of the Devanagari script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptDeva extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Deva';
	}

	/** {@inheritdoc} */
	public function numerals() {
		return array('०', '१', '२', '३', '४', '५', '६', '७', '८', '९');
	}

	/** {@inheritdoc} */
	public function number() {
		return '315';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Devanagari';
	}
}
