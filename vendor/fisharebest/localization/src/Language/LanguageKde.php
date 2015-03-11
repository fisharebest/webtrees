<?php namespace Fisharebest\Localization;

/**
 * Class LanguageKde - Representation of the Makonde language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageKde extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'kde';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryTz;
	}
}
