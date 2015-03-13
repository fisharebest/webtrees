<?php namespace Fisharebest\Localization;

/**
 * Class LanguageRwk - Representation of the Rwa language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageRwk extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'rwk';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryTz;
	}
}
