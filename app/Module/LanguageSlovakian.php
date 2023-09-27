<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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

use Fisharebest\Localization\Locale\LocaleInterface;
use Fisharebest\Localization\Locale\LocaleSk;
use Fisharebest\Webtrees\Relationship;

use function mb_substr;
use function str_repeat;
use function str_starts_with;

/**
 * Class LanguageSlovakian.
 */
class LanguageSlovakian extends AbstractModule implements ModuleLanguageInterface
{
    use ModuleLanguageTrait;

    protected const MALE_COUSINS = [
        ['', ''],
        ['bratranec', '%s bratranca'],
        ['druhostupňový bratranec', '%s druhostupňového bratranca'],
        ['bratranec z 3. kolena', '%s bratranca z 3. kolena'],
        ['bratranec zo 4. kolena', '%s bratranca zo 4. kolena'],
        ['bratranec z 5. kolena', '%s bratranca z 5. kolena'],
        ['bratranec zo 6. kolena', '%s bratranca zo 6. kolena'],
        ['bratranec zo 7. kolena', '%s bratranca zo 7. kolena'],
        ['bratranec z 8. kolena', '%s bratranca z 8. kolena'],
        ['bratranec z 9. kolena', '%s bratranca z 9. kolena'],
        ['bratranec z 10. kolena', '%s bratranca z 10. kolena'],
        ['bratranec z 11. kolena', '%s bratranca z 11. kolena'],
        ['bratranec z 12. kolena', '%s bratranca z 12. kolena'],
        ['bratranec z 13. kolena', '%s bratranca z 13. kolena'],
        ['bratranec zo 14. kolena', '%s bratranca zo 14. kolena'],
        ['bratranec z 15. kolena', '%s bratranca z 15. kolena'],
        ['bratranec zo 16. kolena', '%s bratranca zo 16. kolena'],
        ['bratranec zo 17. kolena', '%s bratranca zo 17. kolena'],
    ];

    protected const FEMALE_COUSINS = [
        ['', ''],
        ['sesternica', '%s sesternice'],
        ['druhostupňová sesternica', '%s druhostupňovej sesternice'],
        ['sesternica z 3. kolena', '%s sesternice z 3. kolena'],
        ['sesternica zo 4. kolena', '%s sesternice zo 4. kolena'],
        ['sesternica z 5. kolena', '%s sesternice z 5. kolena'],
        ['sesternica zo 6. kolena', '%s sesternice zo 6. kolena'],
        ['sesternica zo 7. kolena', '%s sesternice zo 7. kolena'],
        ['sesternica z 8. kolena', '%s sesternice z 8. kolena'],
        ['sesternica z 9. kolena', '%s sesternice z 9. kolena'],
        ['sesternica z 10. kolena', '%s sesternice z 10. kolena'],
        ['sesternica z 11. kolena', '%s sesternice z 11. kolena'],
        ['sesternica z 12. kolena', '%s sesternice z 12. kolena'],
        ['sesternica z 13. kolena', '%s sesternice z 13. kolena'],
        ['sesternica zo 14. kolena', '%s sesternice zo 14. kolena'],
        ['sesternica z 15. kolena', '%s sesternice z 15. kolena'],
        ['sesternica zo 16. kolena', '%s sesternice zo 16. kolena'],
        ['sesternica zo 17. kolena', '%s sesternice zo 17. kolena'],
    ];

    /**
     * Phone-book ordering of letters.
     *
     * @return array<int,string>
     */
    public function alphabet(): array
    {
        return ['A', 'Á', 'Ä', 'B', 'C', 'Č', 'D', 'Ď', 'DZ', 'DŽ', 'E', 'É', 'F', 'G', 'H', 'CH', 'I', 'Í', 'J', 'K', 'L', 'Ľ', 'Ĺ', 'M', 'N', 'Ň', 'O', 'Ó', 'Ô', 'P', 'Q', 'R', 'Ŕ', 'S', 'Š', 'T', 'Ť', 'U', 'Ú', 'V', 'W', 'X', 'Y', 'Ý', 'Z', 'Ž'];
    }

    /**
     * Some languages use digraphs and trigraphs.
     *
     * @param string $string
     *
     * @return string
     */
    public function initialLetter(string $string): string
    {
        foreach (['CH', 'DZ', 'DŽ'] as $digraph) {
            if (str_starts_with($string, $digraph)) {
                return $digraph;
            }
        }

        return mb_substr($string, 0, 1);
    }

    /**
     * @return LocaleInterface
     */
    public function locale(): LocaleInterface
    {
        return new LocaleSk();
    }
    /**
     * @return array<Relationship>
     */
    public function relationships(): array
    {
        $pra = static fn (int $n, string $nominative, string $genitive): array => [
            ($n > 3 ? 'pra ×' . $n . ' ' : str_repeat('pra-', $n)) . $nominative,
            ($n > 3 ? 'pra ×' . $n . ' ' : str_repeat('pra-', $n)) . $genitive,
        ];

        $cousin = static fn (int $n, array $cousins, string $nominative, string $genitive): array => $cousins[$n] ?? [
            $nominative . ' z ' . $n . '. kolena',
            $genitive . '%s z ' . $n . '. kolena',
        ];

        return [
            // Parents
            Relationship::fixed('otec', '%s otca')->father(),
            Relationship::fixed('matka', '%s matky')->mother(),
            Relationship::fixed('rodič', '%s rodiča')->parent(),
            // Children
            Relationship::fixed('syn', '%s syna')->son(),
            Relationship::fixed('dcéra', '%s dcéry')->daughter(),
            Relationship::fixed('dieťa', '%s dieťaťa')->child(),
            // Siblings
            Relationship::fixed('brat', '%s brata')->brother(),
            Relationship::fixed('sestra', '%s sestry')->sister(),
            Relationship::fixed('súrodenec', '%s súrodenca')->sibling(),
            // Divorced partners
            Relationship::fixed('exmanželka', '%s exmanželky')->divorced()->partner()->female(),
            Relationship::fixed('exmanžel', '%s exmanžela')->divorced()->partner()->male(),
            Relationship::fixed('exmanžel/manželka', '%s exmanžela/manželky')->divorced()->partner(),
            // Engaged partners
            Relationship::fixed('snúbenec', '%s snúbence')->engaged()->partner()->female(),
            Relationship::fixed('snúbenica', '%s snúbenice')->engaged()->partner()->male(),
            // Married parters
            Relationship::fixed('manželka', '%s manželky')->wife(),
            Relationship::fixed('manžel', '%s manžela')->husband(),
            Relationship::fixed('manžel/manželka', '%s manžela/manželky')->spouse(),
            Relationship::fixed('partnerka', '%s partnerky')->partner()->female(),
            // Unmarried partners
            Relationship::fixed('partner', '%s partnera')->partner(),
            // In-laws
            Relationship::fixed('tesť', '%s tesťa')->wife()->father(),
            Relationship::fixed('testiná', '%s testinej')->wife()->mother(),
            Relationship::fixed('svokor', '%s svokra')->spouse()->father(),
            Relationship::fixed('svokra', '%s svokry')->spouse()->mother(),
            Relationship::fixed('zať', '%s zaťa')->child()->husband(),
            Relationship::fixed('nevesta', '%s nevesty')->child()->wife(),
            Relationship::fixed('švagor', '%s švagra')->spouse()->brother(),
            Relationship::fixed('švagor', '%s švagra')->sibling()->husband(),
            Relationship::fixed('švagriná', '%s švagrinej')->spouse()->sister(),
            Relationship::fixed('švagriná', '%s švagrinej')->sibling()->wife(),
            // Half-siblings
            Relationship::fixed('nevlastný brat', '%s nevlastného brata')->parent()->son(),
            Relationship::fixed('nevlastná sestra', '%s nevlastnej sestry')->parent()->daughter(),
            Relationship::fixed('nevlastný súrodenec', '%s nevlastného súrodenca')->parent()->child(),
            // Grandparents
            Relationship::fixed('starý otec', '%s starého otca')->parent()->father(),
            Relationship::fixed('stará matka', '%s starej matky')->parent()->mother(),
            Relationship::fixed('starý rodič', '%s starého rodiča')->parent()->parent(),
            // Great-grandparents
            Relationship::fixed('prastarý otec', '%s prastarého otca')->parent()->parent()->father(),
            Relationship::fixed('prastarý otec', '%s prastarého otca')->parent()->parent()->mother(),
            Relationship::fixed('prastarý otec', '%s prastarého otca')->parent()->parent()->parent(),
            // Ancestors
            Relationship::dynamic(static fn (int $n) => $pra($n - 1, 'prastarý otec', '%s prastarého otca'))->ancestor()->male(),
            Relationship::dynamic(static fn (int $n) => $pra($n - 1, 'prastará matka', '%s prastarej matky'))->ancestor()->female(),
            Relationship::dynamic(static fn (int $n) => $pra($n - 1, 'prastarý rodič', '%s prastarého rodiča'))->ancestor(),
            // Grandchildren
            Relationship::fixed('vnuk', '%s vnuka')->child()->son(),
            Relationship::fixed('vnučka', '%s vnučky')->child()->daughter(),
            Relationship::fixed('vnúča', '%s vnúčaťa')->child()->child(),
            // Great-grandchildren
            Relationship::fixed('pravnuk', '%s pravnuka')->child()->child()->son(),
            Relationship::fixed('pravnučka', '%s pravnučky')->child()->child()->daughter(),
            Relationship::fixed('pravnúča', '%s pravnúčaťa')->child()->child()->child(),
            // Descendants
            Relationship::dynamic(static fn (int $n) => $pra($n - 1, 'pravnuk', '%s pravnuka'))->ancestor()->male(),
            Relationship::dynamic(static fn (int $n) => $pra($n - 1, 'pravnučka', '%s pravnučky'))->ancestor()->female(),
            Relationship::dynamic(static fn (int $n) => $pra($n - 1, 'pravnúča', '%s pravnúčaťa'))->ancestor(),
            // Aunts and uncles
            Relationship::fixed('ujo', '%s uja')->mother()->brother(),
            Relationship::fixed('ujčiná', '%s ujčinej')->mother()->brother()->wife(),
            Relationship::fixed('stryná', '%s strynej')->father()->brother()->wife(),
            Relationship::fixed('strýko', '%s strýka')->parent()->brother(),
            Relationship::fixed('teta', '%s tety')->parent()->sister(),
            // Great-aunts and great-uncles
            Relationship::dynamic(static fn (int $n) => $pra($n - 2, 'prastrýko', '%s prastrýka'))->ancestor()->brother(),
            Relationship::dynamic(static fn (int $n) => $pra($n - 2, 'prateta', '%s pratety'))->ancestor()->sister(),
            // Nieces and nephews
            Relationship::fixed('synovec', '%s synovca')->sibling()->son(),
            Relationship::fixed('neter', '%s netere')->sibling()->daughter(),
            // Great-nieces and great-nephews
            Relationship::fixed('prasynovec', '%s prasynovca')->sibling()->child()->son(),
            Relationship::fixed('praneter', '%s pranetere')->sibling()->child()->daughter(),
            Relationship::dynamic(static fn (int $n) => $pra($n - 2, 'prasynovec', '%s prasynovca'))->sibling()->descendant()->son(),
            Relationship::dynamic(static fn (int $n) => $pra($n - 2, 'praneter', '%s pranetere'))->sibling()->descendant()->daughter(),
            // Cousins
            Relationship::dynamic(static fn (int $n): array => $cousin($n, static::FEMALE_COUSINS, '', ''))->symmetricCousin()->female(),
            Relationship::dynamic(static fn (int $n): array => $cousin($n, static::MALE_COUSINS, '', ''))->symmetricCousin()->male(),
            Relationship::dynamic(static fn (int $n): array => $cousin($n, static::MALE_COUSINS, '', ''))->symmetricCousin(),
        ];
    }
}
