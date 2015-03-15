<?php namespace Fisharebest\Localization;

/**
 * Class LanguageNaq - Representation of the Nama (Namibia) language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageNaq extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'naq';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryNa;
	}
}
