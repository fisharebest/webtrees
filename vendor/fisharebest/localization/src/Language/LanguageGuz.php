<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\Territory\TerritoryKe;

/**
 * Class LanguageGuz - Representation of the Gusii language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageGuz extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'guz';
	}

	public function defaultTerritory() {
		return new TerritoryKe;
	}
}
