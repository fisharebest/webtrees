<?php namespace Fisharebest\Localization;

/**
 * Class LanguageNl - Representation of the Dutch language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageNl extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'nl';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryNl;
	}
}
