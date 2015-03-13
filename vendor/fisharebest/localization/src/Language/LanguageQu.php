<?php namespace Fisharebest\Localization;

/**
 * Class LanguageQu - Representation of the Quechua language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageQu extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'qu';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryPe;
	}
}
