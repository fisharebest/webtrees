<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptKhoj - Representation of the Khojki script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptKhoj extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Khoj';
	}

	/** {@inheritdoc} */
	public function number() {
		return '322';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Khojki';
	}
}
