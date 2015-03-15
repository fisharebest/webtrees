<?php namespace Fisharebest\Localization;

/**
 * Class LanguageKab - Representation of the Kabyle language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageKab extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'kab';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryDz;
	}
}
