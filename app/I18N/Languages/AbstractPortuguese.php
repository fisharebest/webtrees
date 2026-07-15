<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
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

namespace Fisharebest\Webtrees\I18N\Languages;

use Fisharebest\Webtrees\Encodings\UTF8;
use Fisharebest\Webtrees\Enums\PluralRule;
use Fisharebest\Webtrees\Relationship;

abstract readonly class AbstractPortuguese extends AbstractLanguage
{
    protected const PluralRule PLURAL_RULE = PluralRule::TwoFormsSingularForOne;

    protected const string DATE_ABOUT         = 'por volta de %s';
    protected const string DATE_AFTER         = 'depois de %s';
    protected const string DATE_BEFORE        = 'antes de %s';
    protected const string DATE_BETWEEN_AND   = 'entre %s e %s';
    protected const string DATE_CALCULATED    = 'calculado em %s';
    protected const string DATE_ESTIMATED     = 'estimado em %s';
    protected const string DATE_FROM          = 'de %s';
    protected const string DATE_FROM_TO       = 'de %s até %s';
    protected const string DATE_INTERPRETED   = 'interpretado em %s';
    protected const string DATE_TO            = 'até %s';
    protected const string ERA_BCE            = '%s' . UTF8::NO_BREAK_SPACE . 'AEC';
    protected const string ERA_CE             = '%s' . UTF8::NO_BREAK_SPACE . 'EC';
    protected const string LIST_SEPARATOR_AND = ' e ';

    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        'Janeiro',
        'Fevereiro',
        'Março',
        'Abril',
        'Maio',
        'Junho',
        'Julho',
        'Agosto',
        'Setembro',
        'Outubro',
        'Novembro',
        'Dezembro',
    ];

    protected const array GREGORIAN_MONTHS_GENITIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_LOCATIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_INSTRUMENTAL = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_NOMINATIVE = [
        '',
        'Tishrei',
        'Cheshvan',
        'Kislev',
        'Tevet',
        'Shevat',
        'Adar I',
        'Adar II',
        'Adar',
        'Nissan',
        'Iyar',
        'Sivan',
        'Tamuz',
        'Av',
        'Elul',
    ];

    protected const array JEWISH_MONTHS_GENITIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_LOCATIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_INSTRUMENTAL = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_NOMINATIVE = [
        '',
        'Vindimiário',
        'Brumário',
        'Frimário',
        'Nivoso',
        'Pluvioso',
        'Ventoso',
        'Germinal',
        'Florial',
        'Pradial',
        'Messidor',
        'Termidor',
        'Fructidor',
        'dias complementares',
    ];

    protected const array FRENCH_MONTHS_GENITIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_LOCATIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_INSTRUMENTAL = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_NOMINATIVE = [
        '',
        'Farvardin',
        'Ordibehesht',
        'Khordad',
        'Tir',
        'Mordad',
        'Shahrivar',
        'Mehr',
        'Aban',
        'Azar',
        'Dey',
        'Bahman',
        'Esfand',
    ];

    protected const array JALALI_MONTHS_GENITIVE = self::JALALI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_LOCATIVE = self::JALALI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_INSTRUMENTAL = self::JALALI_MONTHS_NOMINATIVE;

    /**
     * @return array<Relationship>
     */
    public function relationships(): array
    {
        // Portuguese genitive: "da" (f), "do" (m)
        $da = static fn (string $s): array => [$s, '%s da ' . $s];
        $do = static fn (string $s): array => [$s, '%s do ' . $s];

        $great = static fn (int $n, string $suffix, string $article): array => [
            ($n === 1 ? 'bis' : ($n === 2 ? 'tris' : ($n === 3 ? 'tetra' : $n . '°'))) . $suffix,
            '%s ' . $article . ($n === 1 ? 'bis' : ($n === 2 ? 'tris' : ($n === 3 ? 'tetra' : $n . '°'))) . $suffix,
        ];

        return [
            // Adopted
            Relationship::fixed(...$da('mãe adotiva'))->adoptive()->mother(),
            Relationship::fixed(...$do('pai adotivo'))->adoptive()->father(),
            Relationship::fixed(...$do('pai/mãe adotivo/a'))->adoptive()->parent(),
            Relationship::fixed(...$da('filha adotiva'))->adopted()->daughter(),
            Relationship::fixed(...$do('filho adotivo'))->adopted()->son(),
            Relationship::fixed(...$do('filho/a adotivo/a'))->adopted()->child(),
            // Parents
            Relationship::fixed(...$da('mãe'))->mother(),
            Relationship::fixed(...$do('pai'))->father(),
            Relationship::fixed(...$do('pai/mãe'))->parent(),
            // Children
            Relationship::fixed(...$da('filha'))->daughter(),
            Relationship::fixed(...$do('filho'))->son(),
            Relationship::fixed(...$do('filho/a'))->child(),
            // Siblings
            Relationship::fixed(...$da('irmã gêmea'))->twin()->sister(),
            Relationship::fixed(...$do('irmão gêmeo'))->twin()->brother(),
            Relationship::fixed(...$do('gêmeo/a'))->twin()->sibling(),
            Relationship::fixed(...$da('irmã mais velha'))->older()->sister(),
            Relationship::fixed(...$do('irmão mais velho'))->older()->brother(),
            Relationship::fixed(...$do('irmão/ã mais velho/a'))->older()->sibling(),
            Relationship::fixed(...$da('irmã mais nova'))->younger()->sister(),
            Relationship::fixed(...$do('irmão mais novo'))->younger()->brother(),
            Relationship::fixed(...$do('irmão/ã mais novo/a'))->younger()->sibling(),
            Relationship::fixed(...$da('irmã'))->sister(),
            Relationship::fixed(...$do('irmão'))->brother(),
            Relationship::fixed(...$do('irmão/ã'))->sibling(),
            // Half-siblings
            Relationship::fixed(...$da('meia-irmã'))->parent()->daughter(),
            Relationship::fixed(...$do('meio-irmão'))->parent()->son(),
            Relationship::fixed(...$do('meio/a-irmão/ã'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$da('madrasta'))->parent()->wife(),
            Relationship::fixed(...$do('padrasto'))->parent()->husband(),
            Relationship::fixed(...$do('padrasto/madrasta'))->parent()->married()->spouse(),
            Relationship::fixed(...$da('enteada'))->married()->spouse()->daughter(),
            Relationship::fixed(...$do('enteado'))->married()->spouse()->son(),
            Relationship::fixed(...$do('enteado/a'))->married()->spouse()->child(),
            // Partners
            Relationship::fixed(...$da('ex-esposa'))->divorced()->partner()->female(),
            Relationship::fixed(...$do('ex-marido'))->divorced()->partner()->male(),
            Relationship::fixed(...$do('ex-cônjuge'))->divorced()->partner(),
            Relationship::fixed(...$da('noiva'))->engaged()->partner()->female(),
            Relationship::fixed(...$do('noivo'))->engaged()->partner()->male(),
            Relationship::fixed(...$da('esposa'))->wife(),
            Relationship::fixed(...$do('marido'))->husband(),
            Relationship::fixed(...$do('cônjuge'))->spouse(),
            Relationship::fixed(...$do('companheiro/a'))->partner(),
            // In-laws
            Relationship::fixed(...$da('sogra'))->married()->spouse()->mother(),
            Relationship::fixed(...$do('sogro'))->married()->spouse()->father(),
            Relationship::fixed(...$do('sogro/a'))->married()->spouse()->parent(),
            Relationship::fixed(...$da('nora'))->child()->wife(),
            Relationship::fixed(...$do('genro'))->child()->husband(),
            Relationship::fixed(...$do('genro/nora'))->child()->married()->spouse(),
            Relationship::fixed(...$da('cunhada'))->spouse()->sister(),
            Relationship::fixed(...$do('cunhado'))->spouse()->brother(),
            Relationship::fixed(...$da('cunhada'))->sibling()->wife(),
            Relationship::fixed(...$do('cunhado'))->sibling()->husband(),
            // Grandparents
            Relationship::fixed(...$da('avó'))->parent()->mother(),
            Relationship::fixed(...$do('avô'))->parent()->father(),
            Relationship::fixed(...$do('avô/avó'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$da('neta'))->child()->daughter(),
            Relationship::fixed(...$do('neto'))->child()->son(),
            Relationship::fixed(...$do('neto/a'))->child()->child(),
            // Aunts and uncles
            Relationship::fixed(...$da('tia'))->parent()->sister(),
            Relationship::fixed(...$do('tio'))->parent()->brother(),
            // Nieces and nephews
            Relationship::fixed(...$da('sobrinha'))->sibling()->daughter(),
            Relationship::fixed(...$do('sobrinho'))->sibling()->son(),
            Relationship::fixed(...$da('sobrinha'))->married()->spouse()->sibling()->daughter(),
            Relationship::fixed(...$do('sobrinho'))->married()->spouse()->sibling()->son(),
            // Cousins
            Relationship::fixed(...$da('prima'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$do('primo'))->parent()->sibling()->son(),
            // Dynamic relationships
            Relationship::dynamic(static fn (int $n) => $great($n - 1, 'avó', 'da '))->ancestor()->female(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, 'avô', 'do '))->ancestor()->male(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, 'avô/avó', 'do '))->ancestor(),
            Relationship::dynamic(static fn (int $n) => $great($n - 2, 'neta', 'da '))->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $great($n - 2, 'neto', 'do '))->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $great($n - 2, 'neto/a', 'do '))->descendant(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, 'tia', 'da '))->ancestor()->sister(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, 'tio', 'do '))->ancestor()->brother(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, 'sobrinha', 'da '))->sibling()->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, 'sobrinha', 'da '))->married()->spouse()->sibling()->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, 'sobrinho', 'do '))->sibling()->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, 'sobrinho', 'do '))->married()->spouse()->sibling()->descendant()->male(),
        ];
    }
}
