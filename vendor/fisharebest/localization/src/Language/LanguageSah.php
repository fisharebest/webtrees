<?php namespace Fisharebest\Localization;

/**
 * Class LanguageSah - Representation of the Yakut language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageSah extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'sah';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryRu;
	}
}
