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
		return array(
			'0' => '०',
			'1' => '१',
			'2' => '२',
			'3' => '३',
			'4' => '४',
			'5' => '५',
			'6' => '६',
			'7' => '७',
			'8' => '८',
			'9' => '९',
		);
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
