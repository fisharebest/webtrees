<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule7;
use Fisharebest\Localization\Script\ScriptCyrl;
use Fisharebest\Localization\Territory\TerritoryBy;

/**
 * Class LanguageBe - Representation of the Belarusian language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageBe extends AbstractLanguage implements LanguageInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'be';
	}

	/** {@inheritdoc} */
	public function defaultScript() {
		return new ScriptCyrl;
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryBy;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule7;
	}
}
