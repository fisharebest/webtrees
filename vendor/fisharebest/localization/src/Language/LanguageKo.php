<?php namespace Fisharebest\Localization;

/**
 * Class LanguageKo - Representation of the Korean language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageKo extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'ko';
	}

	/** {@inheritdoc} */
	public function defaultScript() {
		return new ScriptKore;
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryKr;
	}
}
