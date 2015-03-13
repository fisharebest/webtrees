<?php namespace Fisharebest\Localization;

/**
 * Class LanguageBs - Representation of the Bosnian language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageBs extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'bs';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryBa;
	}
}
