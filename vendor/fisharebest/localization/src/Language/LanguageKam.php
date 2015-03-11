<?php namespace Fisharebest\Localization;

/**
 * Class LanguageKam - Representation of the Kamba (Kenya) language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageKam extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'kam';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryKe;
	}
}
