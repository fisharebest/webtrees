<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Script\ScriptDeva;
use Fisharebest\Localization\Territory\TerritoryNp;

/**
 * Class LanguageNe - Representation of the Nepali language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageNe extends AbstractLanguage implements LanguageInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'ne';
	}

	/** {@inheritdoc} */
	public function defaultScript() {
		return new ScriptDeva;
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryNp;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule1;
	}
}
