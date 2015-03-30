<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Script\ScriptCyrl;
use Fisharebest\Localization\Territory\TerritoryKz;

/**
 * Class LanguageKk - Representation of the Kazakh language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageKk extends AbstractLanguage implements LanguageInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'kk';
	}

	/** {@inheritdoc} */
	public function defaultScript() {
		return new ScriptCyrl;
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryKz;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule1;
	}
}
