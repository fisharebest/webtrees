<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Script\ScriptThaa;
use Fisharebest\Localization\Territory\TerritoryMv;

/**
 * Class LanguageDv - Representation of the Divehi language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageDv extends AbstractLanguage implements LanguageInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'dv';
	}

	/** {@inheritdoc} */
	public function defaultScript() {
		return new ScriptThaa;
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryMv;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule1;
	}
}
