<?php namespace Fisharebest\Localization;

/**
 * Class LanguageLkt - Representation of the Lakota language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageLkt extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'lkt';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryUs;
	}
}
