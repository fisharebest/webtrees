<?php namespace Fisharebest\Localization;

/**
 * Class LanguageFr - Representation of the French language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageFr extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'fr';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryFr;
	}
}
