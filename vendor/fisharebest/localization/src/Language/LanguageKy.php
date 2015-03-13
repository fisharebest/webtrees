<?php namespace Fisharebest\Localization;

/**
 * Class LanguageKy - Representation of the Kirghiz language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageKy extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'ky';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryKg;
	}
}
