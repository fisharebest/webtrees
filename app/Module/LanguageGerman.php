<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Localization\Locale\LocaleDe;
use Fisharebest\Localization\Locale\LocaleInterface;
use Fisharebest\Webtrees\Relationship;
use Fisharebest\Webtrees\Encodings\UTF8;
// use Illuminate\Database\Query\Builder;

/**
 * Class LanguageGerman.
 */
class LanguageGerman extends AbstractModule implements ModuleLanguageInterface
{
    use ModuleLanguageTrait;

    /**
     * @return LocaleInterface
     */
    public function locale(): LocaleInterface
    {
        return new LocaleDe();
    }

    /**
     * Letters with diacritics that are considered distinct letters in this language.
     *
     * @return array<string,string>
     */
    protected function normalizeExceptions(): array
    {
        return [
            'A' . UTF8::COMBINING_DIAERESIS    => 'AE',
            'O' . UTF8::COMBINING_DIAERESIS    => 'OE',
            'U' . UTF8::COMBINING_DIAERESIS    => 'UE',
            UTF8::LATIN_CAPITAL_LETTER_SHARP_S => 'SS',
            'a' . UTF8::COMBINING_DIAERESIS    => 'ae',
            'o' . UTF8::COMBINING_DIAERESIS    => 'oe',
            'u' . UTF8::COMBINING_DIAERESIS    => 'ue',
            UTF8::LATIN_SMALL_LETTER_SHARP_S   => 'ss',
        ];
    }
	public function relationships(): array
    {
        // returns array => [nominativ, genitive %s] 
        // $genitive = static fn (string $prefix, string $suffix): array => [$prefix . $suffix, $prefix . 's' . $suffix . '%s'];
        $genitive = static fn (string $prefix, string $suffix, int $gender): array =>			
		    ($gender == 0) ? [$prefix . $suffix, '%s' . ' des ' . $prefix . 's' . $suffix] : (($gender == 1) ? [$prefix . $suffix, '%s' . ' der ' . $prefix . $suffix] : [$prefix . $suffix, '%s' . ' der ' . $prefix . $suffix]);
		
        $ur = static fn (int $n, string $simpleGreat, string $suffix, int $gender): array => $genitive(
		    (($n > 1) ?  ($n + 1) . ' x Ur' : (($n > -1) ? 'Ur' . str_repeat('ur', $n) : '')) . $simpleGreat, $suffix, $gender
			// $n <= -1 -> ''
			// $n == 0 -> Ur 
			// $n == 1 -> Urur 
			// $n >= 2 -> $n+1 ' x Ur'
        );

        return [
            // Adopted
            Relationship::fixed('Adoptivmutter', '%s der Adoptivmutter')->adoptive()->mother(),
            Relationship::fixed('Adoptivvater', '%s des Adoptivvaters')->adoptive()->father(),
            Relationship::fixed('Adoptiveltern', '%s der Adoptiveltern')->adoptive()->parent(),
            Relationship::fixed('Adoptivtochter', '%s der Adoptivtochter')->adopted()->daughter(),
            Relationship::fixed('Adoptivsohn', '%s des Adoptivsohnes')->adopted()->son(),
            Relationship::fixed('Adoptivkind', '%s des Adoptivkindes')->adopted()->child(),
            // Fostered
            Relationship::fixed('Pflegemutter', '%s der Pflegemutter')->fostering()->mother(),
            Relationship::fixed('Pflegevater', '%s des Pflegevaters')->fostering()->father(),
            Relationship::fixed('Pflegeeltern', '%s der Pflegeeltern')->fostering()->parent(),
            Relationship::fixed('Pflegetochter', '%s der Pflegetochter')->fostered()->daughter(),
            Relationship::fixed('Pflegesohn', '%s des Pflegesohnes')->fostered()->son(),
            Relationship::fixed('Pflegekind', '%s des Pflegekindes')->fostered()->child(),
            // Parents
            Relationship::fixed('Mutter', '%s der Mutter')->mother(),
            Relationship::fixed('Vater', '%s des Vaters')->father(),
            Relationship::fixed('Elternteil', '%s der Eltern')->parent(),
            // Children
            Relationship::fixed('Tochter', '%s der Tochter')->daughter(),
            Relationship::fixed('Sohn', '%s des Sohnes')->son(),
            Relationship::fixed('Kind', '%s des Kindes')->child(),
            // Siblings
            Relationship::fixed('Zwillingsschwester', '%s der Zwillingsschwester')->twin()->sister(),
            Relationship::fixed('Zwillingsbruder', '%s des Zwillingsbruders')->twin()->brother(),
            Relationship::fixed('Zwillingsgeschwister', '%s des Zwillingsgeschwisters')->twin()->sibling(),
            Relationship::fixed('Ältere Schwester', '%s der älterer Schwester')->older()->sister(),
            Relationship::fixed('Älterer Bruder', '%s des älteren Bruders')->older()->brother(),
            Relationship::fixed('Älteres Geschwister', '%s des älteren Geschwisters')->older()->sibling(),
            Relationship::fixed('Jüngere Schwester', '%s der jüngeren Schwester')->younger()->sister(),
            Relationship::fixed('Jüngerer Bruder', '%s des jüngeren Bruders')->younger()->brother(),
            Relationship::fixed('Jüngeres Geschwister', '%s des jüngeres Geschwisters')->younger()->sibling(),
            Relationship::fixed('Schwester', '%s der Schwester')->sister(),
            Relationship::fixed('Bruder', '%s des Bruders')->brother(),
            Relationship::fixed('Geschwister', '%s der Geschwister')->sibling(),
            // Half-siblings
            Relationship::fixed('Halbschwester', '%s der Halbschwester')->parent()->daughter(),
            Relationship::fixed('Halbbruder', '%s des Halbbruders')->parent()->son(),
            Relationship::fixed('Halbgeschwister', '%s der Halbgeschwister')->parent()->child(),
            // Stepfamily
            Relationship::fixed('Stiefmutter', '%s der Stiefmutter')->parent()->wife(),
            Relationship::fixed('Stiefvater', '%s des Stiefvaters')->parent()->husband(),
            Relationship::fixed('Stiefelternteil', '%s des Stiefelternteils')->parent()->married()->spouse(),
            Relationship::fixed('Stieftochter', '%s der Stieftochter')->married()->spouse()->daughter(),
            Relationship::fixed('Stiefsohn', '%s des Stiefsohnes')->married()->spouse()->son(),
            Relationship::fixed('Stiefkind', '%s des Stiefkindes')->married()->spouse()->child(),
            Relationship::fixed('Stiefschwester', '%s der Stiefschwester')->parent()->spouse()->daughter(),
            Relationship::fixed('Stiefbruder', '%s des Stiefbruders')->parent()->spouse()->son(),
            Relationship::fixed('Stiefgeschwister', '%s der Stiefgeschwister')->parent()->spouse()->child(),
            // Partners
            Relationship::fixed('Ex-Frau', '%s der Ex-Frau')->divorced()->partner()->female(),
            Relationship::fixed('Ex-Mann', '%s des Ex-Mannes')->divorced()->partner()->male(),
            Relationship::fixed('Ex-Ehepartner', '%s des Ex-Ehepartners')->divorced()->partner(),
            Relationship::fixed('Verlobte', '%s der Verlobten')->engaged()->partner()->female(),
            Relationship::fixed('Verlobter', '%s des Verlobten')->engaged()->partner()->male(),
            Relationship::fixed('Ehefrau', '%s der Ehefrau')->wife(),
            Relationship::fixed('Ehemann', '%s des Ehemannes')->husband(),
            Relationship::fixed('Ehepartner', '%s des Ehepartners')->spouse(),
            Relationship::fixed('Partner', '%s des Partners')->partner(),
            // In-laws
            Relationship::fixed('Schwiegermutter', '%s der Schwiegermutter')->married()->spouse()->mother(),
            Relationship::fixed('Schwiegervater', '%s des Schwiegervaters')->married()->spouse()->father(),
            Relationship::fixed('Schwiegereltern', '%s der Schwiegereltern')->married()->spouse()->parent(),
            Relationship::fixed('Schwiegertochter', '%s der Schwiegertochter')->child()->wife(),
            Relationship::fixed('Schwiegersohn', '%s des Schwiegersohnes')->child()->husband(),
            Relationship::fixed('Schwiegerkind', '%s des Schwiegerkindes')->child()->married()->spouse(),
            Relationship::fixed('Schwägerin', '%s der Schwägerin')->sibling()->spouse()->sister(),
            Relationship::fixed('Schwager', '%s des Schwagers')->sibling()->spouse()->brother(),
            Relationship::fixed('Schwager/Schwägerin', '%s des Schwagers / der Schwägerin')->sibling()->spouse()->sibling(),
            Relationship::fixed('Schwägerin', '%s der Schwägerin')->spouse()->sister(),
            Relationship::fixed('Schwager', '%s des Schwagers')->spouse()->brother(),
            Relationship::fixed('Schwager/Schwägerin', '%s des Schwagers / der Schwägerin')->spouse()->sibling(),
            Relationship::fixed('Schwägerin', '%s der Schwägerin')->sibling()->wife(),
            Relationship::fixed('Schwager', '%s des Schwagers')->sibling()->husband(),
            Relationship::fixed('Schwager/Schwägerin', '%s des Schwagers / der Schwägerin')->sibling()->spouse(),
            // Grandparents
            Relationship::fixed('Großmutter mütterlicherseits', '%s der Großmutter (mütterlicherseits)')->mother()->mother(),
            Relationship::fixed('Großvater mütterlicherseits', '%s des Großvaters (mütterlicherseits)')->mother()->father(),
            Relationship::fixed('Großeltern mütterlicherseits', '%s der Großeltern (mütterlicherseits)')->mother()->parent(),
            Relationship::fixed('Großmutter väterlicherseits', '%s der Großmutter (väterlicherseits)')->father()->mother(),
            Relationship::fixed('Großvater väterlicherseits', '%s des Großvaters (väterlicherseits)')->father()->father(),
            Relationship::fixed('Großeltern väterlicherseits', '%s der Großeltern (väterlicherseits)')->father()->parent(),
            Relationship::fixed('Großmutter', '%s der Großmutter')->parent()->mother(),
            Relationship::fixed('Großvater', '%s des Großvaters')->parent()->father(),
            Relationship::fixed('Großeltern', '%s der Großeltern')->parent()->parent(),
            // Grandchildren
            Relationship::fixed('Enkelin', '%s der Enkelin')->child()->daughter(),
            Relationship::fixed('Enkel', '%s des Enkels')->child()->son(),
            Relationship::fixed('Enkelin/Enkel', '%s der Enkelin/des Enkels')->child()->child(),
			// Nichte / Neffe
            Relationship::fixed('Nichte', '%s der Nichte')->sibling()->daughter(),
            Relationship::fixed('Nichte', '%s der Nichte')->married()->spouse()->sibling()->daughter(),			
            Relationship::fixed('Neffe', '%s des Neffen')->sibling()->son(),
            Relationship::fixed('Neffe', '%s des Neffen')->married()->spouse()->sibling()->son(),
            Relationship::fixed('Nichte/Neffe', '%s der Nichte / des Neffen')->sibling()->child(),
            Relationship::fixed('Nichte/Neffe', '%s der Nichte/ des Neffen')->married()->spouse()->sibling()->child(),
			// Großnichte / Großneffe
            Relationship::fixed('Großnichte', '%s der Großnichte')->sibling()->child()->child()->female(),
            Relationship::fixed('Großnichte', '%s der Großnichte')->married()->spouse()->sibling()->child()->child()->female(),
            Relationship::fixed('Großneffe', '%s des Großneffen')->sibling()->child()->child()->male(),
            Relationship::fixed('Großneffe', '%s des Großneffen')->married()->spouse()->sibling()->child()->child()->male(),
			// Tante / Onkel
            Relationship::fixed('Tante', '%s der Tante')->parent()->sister(),
            Relationship::fixed('Tante', '%s der Tante')->parent()->brother()->wife(),
            Relationship::fixed('Onkel', '%s des Onkels')->parent()->sister()->husband(),
            Relationship::fixed('Onkel', '%s des Onkels')->parent()->brother(),
			// Großtante / Großonkel
            Relationship::fixed('Großtante', '%s der Großtante')->parent()->parent()->sister(),
            Relationship::fixed('Großtante', '%s der Großtante')->parent()->parent()->brother()->wife(),
            Relationship::fixed('Großonkel', '%s des Großonkels')->parent()->parent()->brother(),
            Relationship::fixed('Großonkel', '%s des Großonkels')->parent()->parent()->sister()->husband(),
			// Cousin / Cousine
            Relationship::fixed('Cousine', '%s der Cousine')->parent()->sister()->child()->female(),
            Relationship::fixed('Cousine', '%s der Cousine')->parent()->brother()->child()->female(),
            Relationship::fixed('Cousin', '%s des Cousins')->parent()->sister()->child()->male(),
            Relationship::fixed('Cousin', '%s des Cousins')->parent()->brother()->child()->male(),
            // Relationships with dynamically generated names
			// ancestors: n=2 -> Urgroßmutter (mütterlicherseits) / Großmutter der Mutter
            Relationship::dynamic(static fn (int $n) => $ur($n - 2, 'großmutter', ' (mütterlicherseits) ', 1))->mother()->ancestor()->female(),
            Relationship::dynamic(static fn (int $n) => $ur($n - 2, 'großvater', ' (mütterlicherseits) ', 0))->mother()->ancestor()->male(),
            Relationship::dynamic(static fn (int $n) => $ur($n - 2, 'großmutter', ' (väterlicherseits) ', 1))->father()->ancestor()->female(),
            Relationship::dynamic(static fn (int $n) => $ur($n - 2, 'großvater', ' (väterlicherseits) ', 0))->father()->ancestor()->male(),
			//
            Relationship::dynamic(static fn (int $n) => $ur($n - 2, 'großeltern', ' (väterlicherseits) ', 2))->father()->ancestor(),
            Relationship::dynamic(static fn (int $n) => $ur($n - 2, 'großeltern', ' (mütterlicherseits) ', 2))->mother()->ancestor(),
			//
            Relationship::dynamic(static fn (int $n) => $ur($n - 3, 'großtante', ' ', 1))->ancestor()->sister(),
            Relationship::dynamic(static fn (int $n) => $ur($n - 3, 'großtante', ' ', 1))->ancestor()->sibling()->wife(),
            Relationship::dynamic(static fn (int $n) => $ur($n - 3, 'großonkel', ' ', 0))->ancestor()->brother(),
            Relationship::dynamic(static fn (int $n) => $ur($n - 3, 'großonkel', ' ', 0))->ancestor()->sibling()->husband(),
			// descendants
            Relationship::dynamic(static fn (int $n) => $ur($n - 3, 'großnichte', ' ', 1))->sibling()->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $ur($n - 3, 'großnichte', ' ', 1))->married()->spouse()->sibling()->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $ur($n - 3, 'großneffe', ' ', 0))->sibling()->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $ur($n - 3, 'großneffe'), ' ', 0)->married()->spouse()->sibling()->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $ur($n - 3, 'enkelin', ' ', 1))->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $ur($n - 3, 'enkel', ' ', 0))->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $ur($n - 3, 'enkelin/enkel', ' ', 2))->descendant(),
        ];
    }
}
