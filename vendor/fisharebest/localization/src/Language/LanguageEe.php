<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryGh;

/**
 * Class LanguageEe - Representation of the Ewe language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageEe extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'ee';
	}

	public function defaultTerritory() {
		return new TerritoryGh;
	}

	public function pluralRule() {
		return new PluralRule1;
	}
}
