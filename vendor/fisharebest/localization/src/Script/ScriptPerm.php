<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptPerm - Representation of the Old Permic script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptPerm extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Perm';
	}

	/** {@inheritdoc} */
	public function number() {
		return '227';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Old_Permic';
	}
}
