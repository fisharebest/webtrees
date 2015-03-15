<?php namespace Fisharebest\Localization;

/**
 * Class LanguageEe - Representation of the Ewe language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageEe extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'ee';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryGh;
	}
}
