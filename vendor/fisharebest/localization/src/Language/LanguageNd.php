<?php namespace Fisharebest\Localization;

/**
 * Class LanguageNd - Representation of the North Ndebele language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageNd extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'nd';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryZw;
	}
}
