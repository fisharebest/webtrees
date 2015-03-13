<?php namespace Fisharebest\Localization;

/**
 * Class LocalePaArabPk
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocalePaArabPk extends LocalePaArab {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryPk;
	}
}
