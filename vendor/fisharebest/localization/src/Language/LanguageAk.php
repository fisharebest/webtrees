<?php namespace Fisharebest\Localization;

/**
 * Class LanguageAk - Representation of the Akan language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageAk extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'ak';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryGh;
	}
}
