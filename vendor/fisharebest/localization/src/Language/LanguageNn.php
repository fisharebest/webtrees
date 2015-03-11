<?php namespace Fisharebest\Localization;

/**
 * Class LanguageNn - Representation of the Norwegian Nynorsk language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageNn extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'nn';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryNo;
	}
}
