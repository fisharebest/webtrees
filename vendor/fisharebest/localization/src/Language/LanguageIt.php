<?php namespace Fisharebest\Localization;

/**
 * Class LanguageIt - Representation of the Italian language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageIt extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'it';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryIt;
	}
}
