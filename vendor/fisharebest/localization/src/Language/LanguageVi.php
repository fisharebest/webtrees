<?php namespace Fisharebest\Localization;

/**
 * Class LanguageVi - Representation of the Vietnamese language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageVi extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'vi';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryVn;
	}
}
