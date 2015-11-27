<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\Territory\TerritoryKe;

/**
 * Class LanguageKam - Representation of the Kamba (Kenya) language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageKam extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'kam';
	}

	public function defaultTerritory() {
		return new TerritoryKe;
	}
}
