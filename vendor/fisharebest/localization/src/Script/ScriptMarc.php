<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptMarc - Representation of the Marchen script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptMarc extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Marc';
	}

	/** {@inheritdoc} */
	public function number() {
		return '332';
	}
}
