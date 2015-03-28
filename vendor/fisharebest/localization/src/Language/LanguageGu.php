<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule2;
use Fisharebest\Localization\Script\ScriptGujr;
use Fisharebest\Localization\Territory\TerritoryIn;

/**
 * Class LanguageGu - Representation of the Gujarati language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageGu extends AbstractLanguage implements LanguageInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'gu';
	}

	/** {@inheritdoc} */
	public function defaultScript() {
		return new ScriptGujr;
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryIn;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule2;
	}
}
