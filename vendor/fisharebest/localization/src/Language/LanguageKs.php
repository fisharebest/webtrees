<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Script\ScriptArab;
use Fisharebest\Localization\Territory\TerritoryIn;

/**
 * Class LanguageKs - Representation of the Kashmiri language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageKs extends AbstractLanguage implements LanguageInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'ks';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryIn;
	}

	/** {@inheritdoc} */
	public function defaultScript() {
		return new ScriptArab;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule1;
	}
}
