<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptCprt - Representation of the Cypriot script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptCprt extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Cprt';
	}

	/** {@inheritdoc} */
	public function number() {
		return '403';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Cypriot';
	}
}
