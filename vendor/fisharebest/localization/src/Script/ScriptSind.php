<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptSind - Representation of the Khudawadi, Sindhi script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptSind extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Sind';
	}

	/** {@inheritdoc} */
	public function number() {
		return '318';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Khudawadi';
	}
}
