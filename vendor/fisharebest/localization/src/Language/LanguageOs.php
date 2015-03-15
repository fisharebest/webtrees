<?php namespace Fisharebest\Localization;

/**
 * Class LanguageOs - Representation of the Ossetian language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageOs extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'os';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryRu;
	}
}
