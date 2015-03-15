<?php namespace Fisharebest\Localization;

/**
 * Class LanguageKw - Representation of the Cornish language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageKw extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'kw';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryGb;
	}
}
