<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\Territory\TerritoryKe;

/**
 * Class LanguageLuo - Representation of the Luo (Kenya and Tanzania) language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageLuo extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'luo';
	}

	public function defaultTerritory() {
		return new TerritoryKe;
	}
}
