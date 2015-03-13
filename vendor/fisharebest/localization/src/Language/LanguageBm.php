<?php namespace Fisharebest\Localization;

/**
 * Class LanguageBm - Representation of the Bambara language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageBm extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'bm';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryMl;
	}
}
