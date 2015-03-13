<?php namespace Fisharebest\Localization;

/**
 * Class LanguageLt - Representation of the Lithuanian language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageLt extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'lt';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryLt;
	}
}
