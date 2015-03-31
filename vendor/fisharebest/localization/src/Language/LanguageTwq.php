<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\Territory\TerritoryNe;

/**
 * Class LanguageTwq - Representation of the Tasawaq language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageTwq extends AbstractLanguage implements LanguageInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'twq';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryNe;
	}
}
