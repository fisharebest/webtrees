<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
namespace Fisharebest\Webtrees;

use Fisharebest\Webtrees\SurnameTradition\DefaultSurnameTradition;
use Fisharebest\Webtrees\SurnameTradition\IcelandicSurnameTradition;
use Fisharebest\Webtrees\SurnameTradition\LithuanianSurnameTradition;
use Fisharebest\Webtrees\SurnameTradition\MatrilinealSurnameTradition;
use Fisharebest\Webtrees\SurnameTradition\PaternalSurnameTradition;
use Fisharebest\Webtrees\SurnameTradition\PatrilinealSurnameTradition;
use Fisharebest\Webtrees\SurnameTradition\PolishSurnameTradition;
use Fisharebest\Webtrees\SurnameTradition\PortugueseSurnameTradition;
use Fisharebest\Webtrees\SurnameTradition\SpanishSurnameTradition;
use Fisharebest\Webtrees\SurnameTradition\SurnameTraditionInterface;

/**
 *
 */
class SurnameTradition {
	/**
	 * Create a surname tradition object for a given surname tradition name.
	 *
	 * @param string $name Internal name of the surname tradition
	 *
	 * @return SurnameTraditionInterface
	 */
	public static function create($name) {
		switch ($name) {
		case 'paternal':
			return new PaternalSurnameTradition;
		case 'patrilineal':
			return new PatrilinealSurnameTradition;
		case 'matrilineal':
			return new MatrilinealSurnameTradition;
		case 'portuguese':
			return new PortugueseSurnameTradition;
		case 'spanish':
			return new SpanishSurnameTradition;
		case 'polish':
			return new PolishSurnameTradition;
		case 'lithuanian':
			return new LithuanianSurnameTradition;
		case 'icelandic':
			return new IcelandicSurnameTradition;
		default:
			return new DefaultSurnameTradition;
		}
	}

	/**
	 * A list of known surname traditions, with their descriptions
	 *
	 * @return string[]
	 */
	public static function allDescriptions() {
		return array(
			'paternal' =>
				I18N::translateContext('Surname tradition', 'paternal') .
				' — ' . /* I18N: In the paternal surname tradition, ... */ I18N::translate('Children take their father’s surname.') .
				' ' . /* I18N: In the paternal surname tradition, ... */ I18N::translate('Wives take their husband’s surname.'),
			/* I18N: A system where children take their father’s surname */ 'patrilineal' =>
				I18N::translate('patrilineal') .
				' — ' . /* I18N: In the patrilineal surname tradition, ... */ I18N::translate('Children take their father’s surname.'),
			/* I18N: A system where children take their mother’s surname */ 'matrilineal' =>
				I18N::translate('matrilineal') .
				' — ' . /* I18N: In the matrilineal surname tradition, ... */ I18N::translate('Children take their mother’s surname.'),
			'spanish' =>
				I18N::translateContext('Surname tradition', 'Spanish') .
				' — ' . /* I18N: In the Spanish surname tradition, ... */ I18N::translate('Children take one surname from the father and one surname from the mother.'),
			'portuguese' =>
				I18N::translateContext('Surname tradition', 'Portuguese') .
				' — ' . /* I18N: In the Portuguese surname tradition, ... */ I18N::translate('Children take one surname from the mother and one surname from the father.'),
			'icelandic' =>
				I18N::translateContext('Surname tradition', 'Icelandic') .
				' — ' . /* I18N: In the Icelandic surname tradition, ... */ I18N::translate('Children take a patronym instead of a surname.'),
			'polish' =>
				I18N::translateContext('Surname tradition', 'Polish') .
				' — ' . /* I18N: In the Polish surname tradition, ... */ I18N::translate('Children take their father’s surname.') .
				' ' . /* I18N: In the Polish surname tradition, ... */ I18N::translate('Wives take their husband’s surname.') .
				' ' . /* I18N: In the Polish surname tradition, ... */ I18N::translate('Surnames are inflected to indicate an individual’s gender.'),
			'lithuanian' =>
				I18N::translateContext('Surname tradition', 'Lithuanian') .
				' — ' . /* I18N: In the Lithuanian surname tradition, ... */ I18N::translate('Children take their father’s surname.') .
				' ' . /* I18N: In the Lithuanian surname tradition, ... */ I18N::translate('Wives take their husband’s surname.') .
				' ' . /* I18N: In the Lithuanian surname tradition, ... */ I18N::translate('Surnames are inflected to indicate an individual’s gender and marital status.'),
			'none' =>
				I18N::translateContext('Surname tradition', 'none'),
		);

	}
}
