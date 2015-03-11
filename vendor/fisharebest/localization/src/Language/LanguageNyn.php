<?php namespace Fisharebest\Localization;

/**
 * Class LanguageNyn - Representation of the Nyankole language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageNyn extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'nyn';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryUg;
	}
}
