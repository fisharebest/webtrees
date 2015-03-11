<?php namespace Fisharebest\Localization;

/**
 * Class LanguageLb - Representation of the Luxembourgish language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageLb extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'lb';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryLu;
	}
}
