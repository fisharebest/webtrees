<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptPerm - Representation of the Old Permic script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptPerm extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Perm';
	}

	public function number() {
		return '227';
	}

	public function unicodeName() {
		return 'Old_Permic';
	}
}
