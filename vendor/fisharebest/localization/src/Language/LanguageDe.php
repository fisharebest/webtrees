<?php namespace Fisharebest\Localization;

/**
 * Class LanguageDe - Representation of the German language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageDe extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'de';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryDe;
	}
}
