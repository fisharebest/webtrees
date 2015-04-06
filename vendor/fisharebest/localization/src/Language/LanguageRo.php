<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule5;
use Fisharebest\Localization\Territory\TerritoryRo;

/**
 * Class LanguageRo - Representation of the Romanian language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageRo extends AbstractLanguage implements LanguageInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'ro';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryRo;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule5;
	}
}
