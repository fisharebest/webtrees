<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptKpel - Representation of the Kpelle script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptKpel extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Kpel';
	}

	/** {@inheritdoc} */
	public function number() {
		return '436';
	}
}
