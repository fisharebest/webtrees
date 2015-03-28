<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\Territory\TerritoryPe;

/**
 * Class LanguageQu - Representation of the Quechua language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageQu extends AbstractLanguage implements LanguageInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'qu';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryPe;
	}
}
