<?php namespace Fisharebest\Localization;

/**
 * Class LanguageJmc - Representation of the Machame language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageJmc extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'jmc';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryTz;
	}
}
