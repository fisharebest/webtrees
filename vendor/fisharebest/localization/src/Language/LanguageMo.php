<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule5;
use Fisharebest\Localization\Territory\TerritoryMd;

/**
 * Class LanguageIt - Representation of the Italian language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageMo extends AbstractLanguage implements LanguageInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'mo';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryMd;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule5;
	}
}
