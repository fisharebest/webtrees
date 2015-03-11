<?php namespace Fisharebest\Localization;

/**
 * Class LanguageNso - Representation of the Pedi language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageNso extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'nso';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryZa;
	}
}
