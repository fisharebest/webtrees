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

use Fisharebest\Localization\Locale\LocaleFr;
use Fisharebest\Localization\Locale\LocaleInterface;
use Fisharebest\Webtrees\Relationship;

/**
 * Class LanguageFrench.
 */
class LanguageFrench extends AbstractModule implements ModuleLanguageInterface
{
    use ModuleLanguageTrait;

    protected const SYMMETRIC_COUSINS = [
        1 => [
            'F' => ['cousine germaine', '%s de la cousine germaine'],
            'M' => ['cousin germain', '%s du cousin germain'],
            'U' => ['cousin germain', '%s du cousin germain']
        ],
        2 => [
            'F' => ['cousine issue de germain', '%s de la cousine issue de germain'],
            'M' => ['cousin issu de germain', '%s du cousin issu de germain'],
            'U' => ['cousin issu de germain', '%s du cousin issu de germain']
        ]
    ];

    protected const ASYMMETRIC_COUSINS = [
        1 => [
            'F' => ['down', 'petite-cousine', 'de la '],
            'M' => ['down', 'petit-cousin', 'du '],
            'U' => ['down', 'petit-cousin', 'du ']
        ],
        -1 => [
            'F' => ['up', 'grand-cousine', 'de la '],
            'M' => ['up', 'grand-cousin', 'du '],
            'U' => ['up', 'grand-cousin', 'du ']
        ],
    ];

    /**
     * @return LocaleInterface
     */
    public function locale(): LocaleInterface
    {
        return new LocaleFr();
    }

    /**
     * Pour les traducteurs français, certaines configurations peuvent avoir plusieurs traduction françaises possibles,
     * ou aucune. Voici les choix qui ont été faits (mais complètement ouverts à discussion):
     *
     * - je n'ai aucune intention de rentrer dans le débat de l'écriture inclusive, mais malheureusement un choix doit
     *   être fait: lorsque nécessaire dans les choix des articles ou accords, je m'en suis tenu à la recommandation de
     *   l'Académie Française d'utiliser la forme non marquée (et donc le masculin) pour le genre neutre.
     * - dans le cas de frères/sœurs jumeaux, j'évite le problème en utiliseant le substantif `jumeau` lorsque le sexe
     *   n'est pas connu, alors que j'utilise la structure `frère jumeau`/`sœur jumelle` lorsque le sexe est connu.
     * - `conjoint` a été choisi pour un couple non marié (`époux`/`épouse` lorsque les conjoints sont mariés).
     *   Une alternative est `partenaire`, mais `conjoint` est le terme déjà utilisé dans les traductions françaises.
     * - la notion de `foster` (qui peut traduire plusieurs réalités différentes en français) a été traduite dans le
     *   cadre de la `famille d'accueil`. Les suggestions sont les bienvenues.
     * - La situation des enfants dans les familles recomposées a été traduite:
     *      - `frère`/`sœur` pour les enfants dont les deux parents sont les mêmes
     *      - `demi-frère`/`demi-sœur` pour les enfants qui ont un parent en commun
     *      - `quasi-frère`/`quasi-sœur` pour les enfants qui ne partagent aucun parent en commun, mais dont les parents
     *      sont en couple
     * - la notion d'âge entre frères/sœurs a été traduite par `grand frère`/`petit frère`, plutôt que des variantes sur
     *   `frère aîné`/`frère cadet` ou `frère plus âgé`/`frère plus jeune`
     * - Lorsqu'il est nécessaire d'aller au-delà d'un `arrière-`{substantif} (par exemple, pour décrire le case de
     *   l'enfant d'un arrière-petit-enfant), la forme `{substantif} au Ne degré` est choisie, avec pour convention
     *   N = 1 pour le niveau du substantif racine, on utilisera donc par exemple:
     *      - `petit-enfant` (= petit-enfant au 1er degré)
     *      - `arrière-petit-enfant` (= petit-enfant au 2e degré)
     *      - `petit-enfant au 3e degré` et ainsi de suite pour les degrés supérieurs
     * - Un exception à la règle précédente sont les grand-parents au 3e degré, qui ont la description de `trisaïeux`.
     * - Pour les cousins, c'est la description selon le droit canon qui a été choisie (principalement car elle donne
     *   une meilleure visibilité de la distance à l'ancêtre commun que la description en droit civil), donc:
     *      - l'enfant d'un oncle/tante est un `cousin germain`/`cousine germaine` (= cousins au 1er degré)
     *      - les enfants de cousins germains sont des `cousins issus de germain` (= cousins au 2e degré)
     *      - pour les enfants des cousins issus de germains, et ainsi de suite, la relation est décrite suivant le
     *      nombre de degré séparant les cousins de l'ancêtre commun:
     *      - en cas de symétrie des chemins, ils sont dits `cousins au N-ème degré`
     *      - en cas d'asymétrie des chemins, ils sont dit  `cousins du N-ème au M-ème degré`
     *      - de plus, les notions de `grand-cousin` et `petit-cousin` ont été implémentées comme suit:
     *          - un `(arrière-)grand-cousin` est l'enfant d'un `(arrière-)grand-oncle`/`grand-tante`
     *              (= cousin du 1er au N-ème degré)
     *          - un `(arrière-)petit-cousin` est un `(arrière-)petit-neveu`/`petite-nièce` d'un parent
     *              (= cousin du Ner au 1er degré)
     *
     * @return array<Relationship>
     */
    public function relationships(): array
    {
        // Construct the genitive form in French
        $genitive = static fn (string $s, string $genitive_link): array => [$s, '%s ' . $genitive_link . $s];

        // Functions to coumpound the name that can be indefinitely repeated
        $degree = static fn (int $n, string $suffix, string $genitive_link): array =>
                $genitive($suffix . ' au ' . $n . '<sup>e</sup> degré', $genitive_link);

        $great = static fn (int $n, string $suffix, string $genitive_link): array =>
                $n <= 1 ? $genitive('arrière-' . $suffix, 'de l’') : $degree($n + 1, $suffix, $genitive_link);

        $firstCompound = static fn (int $n, string $suffix, string $genitive_link): array =>
                $n <= 1 ? $genitive($suffix, $genitive_link) : $great($n - 1, $suffix, $genitive_link);

        $compound =
            static fn (int $n, string $first_level, string $suffix, string $genitive_none, string $genitive_first): array =>
                $n <= 1 ? $genitive($suffix, $genitive_none) : $firstCompound($n - 1, $first_level . $suffix, $genitive_first);

        // Functions to translate cousins' degree of relationship
        $symmetricCousin = static fn (int $n, string $sex): array => self::SYMMETRIC_COUSINS[$n][$sex] ?? $genitive(
            $sex === 'F' ? 'cousine au ' . $n . '<sup>e</sup> degré' : 'cousin au ' . $n . '<sup>e</sup> degré',
            $sex === 'F' ? 'de la ' : 'du '
        );

        $cousin =
            static function (int $up, int $down, string $sex) use ($symmetricCousin, $firstCompound, $genitive): array {
                if ($up === $down) {
                    return $symmetricCousin($up, $sex);
                }
                $fixed = self::ASYMMETRIC_COUSINS[$up][$sex] ?? self::ASYMMETRIC_COUSINS[-$down][$sex] ?? null;
                if ($fixed !== null) {
                    $fixed[0] = $fixed[0] === 'up' ? $up - 1 : $down - 1;
                    return $firstCompound(...$fixed);
                }
                return $genitive(
                    $sex === 'F' ?
                        'cousine du ' . $down . '<sup>e</sup> au ' . $up . '<sup>e</sup> degré' :
                        'cousin du ' . $down . '<sup>e</sup> au ' . $up . '<sup>e</sup> degré',
                    $sex === 'F' ? 'de la ' : 'du '
                );
            };

        return [
            // Adopted
            Relationship::fixed('mère adoptive', '%s de la mère adoptive')->adoptive()->mother(),
            Relationship::fixed('père adoptif', '%s du père adoptif')->adoptive()->father(),
            Relationship::fixed('parent adoptif', '%s du parent adoptif')->adoptive()->parent(),
            Relationship::fixed('sœur adoptive', '%s de la sœur adoptive')->adoptive()->sister(),
            Relationship::fixed('frère adoptif', '%s du frère adoptif')->adoptive()->brother(),
            Relationship::fixed('frère/sœur adoptif', '%s du frère/sœur adoptif')->adoptive()->sibling(),
            Relationship::fixed('fille adoptive', '%s de la fille adoptive')->adopted()->daughter(),
            Relationship::fixed('fils adoptif', '%s du fils adoptif')->adopted()->son(),
            Relationship::fixed('enfant adoptif', '%s de l’enfant adoptif')->adopted()->child(),
            Relationship::fixed('sœur adoptive', '%s de la sœur adoptive')->adopted()->sister(),
            Relationship::fixed('frère adoptif', '%s du frère adoptif')->adopted()->brother(),
            Relationship::fixed('frère/sœur adoptif', '%s du frère/sœur adoptif')->adopted()->sibling(),
            // Fostered
            Relationship::fixed('mère d’accueil', '%s de la mère d’acceuil')->fostering()->mother(),
            Relationship::fixed('père d’accueil', '%s du père d’acceuil')->fostering()->father(),
            Relationship::fixed('parent d’accueil', '%s du parent d’acceuil')->fostering()->parent(),
            Relationship::fixed('sœur d’accueil', '%s de la sœur d’accueil')->fostering()->sister(),
            Relationship::fixed('frère d’accueil', '%s du frère d’accueil')->fostering()->brother(),
            Relationship::fixed('frère/sœur d’accueil', '%s du frère/sœur d’accueil')->fostering()->sibling(),
            Relationship::fixed('fille accueillie', '%s de la fille accueillie')->fostered()->daughter(),
            Relationship::fixed('fils accueilli', '%s du fils accueilli')->fostered()->son(),
            Relationship::fixed('enfant accueilli', '%s de l’enfant accueilli')->fostered()->child(),
            Relationship::fixed('sœur accueillie', '%s de la sœur accueillie')->fostered()->sister(),
            Relationship::fixed('frère accueilli', '%s du frère accueilli')->fostered()->brother(),
            Relationship::fixed('frère/sœur accueilli', '%s du frère/sœur accueilli')->fostered()->sibling(),
            // Parents
            Relationship::fixed('mère', '%s de la mère')->mother(),
            Relationship::fixed('père', '%s du père')->father(),
            Relationship::fixed('parent', '%s du parent')->parent(),
            // Children
            Relationship::fixed('fille', '%s de la fille')->daughter(),
            Relationship::fixed('fils', '%s du fils')->son(),
            Relationship::fixed('enfant', '%s de l’enfant')->child(),
            // Siblings
            Relationship::fixed('sœur jumelle', '%s de la sœur jumelle')->twin()->sister(),
            Relationship::fixed('frère jumeau', '%s du frère jumeau')->twin()->brother(),
            Relationship::fixed('jumeau', '%s du jumeau')->twin()->sibling(),
            Relationship::fixed('grande sœur', '%s de la grande sœur')->older()->sister(),
            Relationship::fixed('grand frère', '%s du grand frère')->older()->brother(),
            Relationship::fixed('grand frère/sœur', '%s du grand frère/sœur')->older()->sibling(),
            Relationship::fixed('petite sœur', '%s de la petite sœur')->younger()->sister(),
            Relationship::fixed('petit frère', '%s du petit-frère')->younger()->brother(),
            Relationship::fixed('petit frère/sœur', '%s du petit frère/sœur')->younger()->sibling(),
            Relationship::fixed('sœur', '%s de la sœur')->sister(),
            Relationship::fixed('frère', '%s du frère')->brother(),
            Relationship::fixed('frère/sœur', '%s du frère/sœur')->sibling(),
            // Half-family
            Relationship::fixed('demi-sœur', '%s de la demi-sœur')->parent()->daughter(),
            Relationship::fixed('demi-frère', '%s du demi-frère')->parent()->son(),
            Relationship::fixed('demi-frère/sœur', '%s du demi-frère/sœur')->parent()->child(),
            // Stepfamily
            Relationship::fixed('belle-mère', '%s de la belle-mère')->parent()->wife(),
            Relationship::fixed('beau-père', '%s du beau-père')->parent()->husband(),
            Relationship::fixed('beau-parent', '%s du beau-parent')->parent()->married()->spouse(),
            Relationship::fixed('belle-fille', '%s de la belle-fille')->married()->spouse()->daughter(),
            Relationship::fixed('beau-fils', '%s du beau-fils')->married()->spouse()->son(),
            Relationship::fixed('beau-fils/fille', '%s du beau-fils/fille')->married()->spouse()->child(),
            Relationship::fixed('quasi-sœur', '%s de la quasi-sœur')->parent()->spouse()->daughter(),
            Relationship::fixed('quasi-frère', '%s du quasi-frère')->parent()->spouse()->son(),
            Relationship::fixed('quasi-frère/sœur', '%s du quasi-frère/sœur')->parent()->spouse()->child(),
            // Partners
            Relationship::fixed('ex-épouse', '%s de l’ex-épouse')->divorced()->partner()->female(),
            Relationship::fixed('ex-époux', '%s de l’ex-époux')->divorced()->partner()->male(),
            Relationship::fixed('ex-époux', '%s de l’ex-époux')->divorced()->partner(),
            Relationship::fixed('ex-conjointe', '%s de l’ex-conjoint')->divorced()->partner()->female(),
            Relationship::fixed('ex-conjoint', '%s de l’ex-conjoint')->divorced()->partner()->male(),
            Relationship::fixed('ex-conjoint', '%s de l’ex-conjoint')->divorced()->partner(),
            Relationship::fixed('fiancée', '%s de la fiancée')->engaged()->partner()->female(),
            Relationship::fixed('fiancé', '%s du fiancé')->engaged()->partner()->male(),
            Relationship::fixed('épouse', '%s de l’épouse')->wife(),
            Relationship::fixed('époux', '%s de l’époux')->husband(),
            Relationship::fixed('époux', '%s de l’époux')->spouse(),
            Relationship::fixed('conjointe', '%s du conjoint')->partner()->female(),
            Relationship::fixed('conjoint', '%s du conjoint')->partner()->male(),
            Relationship::fixed('conjoint', '%s du conjoint')->partner(),
            // In-laws
            Relationship::fixed('belle-mère', '%s de la belle-mère')->married()->spouse()->mother(),
            Relationship::fixed('beau-père', '%s du beau-père')->married()->spouse()->father(),
            Relationship::fixed('beau-parent', '%s du beau-parent')->married()->spouse()->parent(),
            Relationship::fixed('bru', '%s de la bru')->child()->wife(),
            Relationship::fixed('gendre', '%s du gendre')->child()->husband(),
            Relationship::fixed('belle-sœur', '%s de la belle-sœur')->spouse()->sister(),
            Relationship::fixed('beau-frère', '%s du beau-frère')->spouse()->brother(),
            Relationship::fixed('beau-frère/belle-sœur', '%s du beau-frère/belle-sœur')->spouse()->sibling(),
            Relationship::fixed('belle-sœur', '%s de la belle-sœur')->sibling()->wife(),
            Relationship::fixed('beau-frère', '%s du beau-frère')->sibling()->husband(),
            Relationship::fixed('beau-frère/belle-sœur', '%s du beau-frère/belle-sœur')->sibling()->spouse(),
            // Grandparents and above
            //"Trisaïeux" are an exception to the dynamic rule
            Relationship::fixed('trisaïeule maternelle', '%s de la trisaïeule maternelle')->mother()->parent()->parent()->mother(),
            Relationship::fixed('trisaïeul maternel', '%s du trisaïeul maternel')->mother()->parent()->parent()->father(),
            Relationship::fixed('trisaïeule paternelle', '%s de la trisaïeule paternelle')->father()->parent()->parent()->mother(),
            Relationship::fixed('trisaïeul paternel', '%s du trisaïeul paternel')->father()->parent()->parent()->father(),
            Relationship::fixed('trisaïeule', '%s de la trisaïeule')->parent()->parent()->parent()->mother(),
            Relationship::fixed('trisaïeul', '%s du trisaïeul')->parent()->parent()->parent()->father(),
            Relationship::dynamic(static fn (int $n) => $firstCompound($n, 'grand-mère maternelle', 'de la '))->mother()->ancestor()->female(),
            Relationship::dynamic(static fn (int $n) => $firstCompound($n, 'grand-père maternel', 'du '))->mother()->ancestor()->male(),
            Relationship::dynamic(static fn (int $n) => $firstCompound($n, 'grand-parent maternel', 'du '))->mother()->ancestor(),
            Relationship::dynamic(static fn (int $n) => $firstCompound($n, 'grand-mère paternelle', 'de la '))->father()->ancestor()->female(),
            Relationship::dynamic(static fn (int $n) => $firstCompound($n, 'grand-père paternel', 'du '))->father()->ancestor()->male(),
            Relationship::dynamic(static fn (int $n) => $firstCompound($n, 'grand-parent paternel', 'du '))->father()->ancestor(),
            Relationship::dynamic(static fn (int $n) => $firstCompound($n, 'grand-mère', 'de la '))->parent()->ancestor()->female(),
            Relationship::dynamic(static fn (int $n) => $firstCompound($n, 'grand-père', 'du '))->parent()->ancestor()->male(),
            Relationship::dynamic(static fn (int $n) => $firstCompound($n, 'grand-parent', 'du '))->parent()->ancestor(),
            // Grandchildren and below
            Relationship::dynamic(static fn (int $n) => $firstCompound($n, 'petite-fille', 'de la '))->child()->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $firstCompound($n, 'petit-fils', 'du '))->child()->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $firstCompound($n, 'petit-enfant', 'du '))->child()->descendant(),
            // Collateral relatives
            Relationship::dynamic(static fn (int $n) => $compound($n, 'grand-', 'tante', 'de la ', 'de la '))->ancestor()->sister(),
            Relationship::dynamic(static fn (int $n) => $compound($n, 'grand-', 'tante par alliance', 'de la ', 'de la '))->ancestor()->sibling()->wife(),
            Relationship::dynamic(static fn (int $n) => $compound($n, 'grand-', 'oncle', 'de l’', 'du '))->ancestor()->brother(),
            Relationship::dynamic(static fn (int $n) => $compound($n, 'grand-', 'oncle par alliance', 'de l’', 'du '))->ancestor()->sibling()->husband(),
            Relationship::dynamic(static fn (int $n) => $compound($n, 'petite-', 'nièce', 'de la ', 'de la '))->sibling()->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $compound($n, 'petite-', 'nièce par alliance', 'de la ', 'de la '))->married()->spouse()->sibling()->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $compound($n, 'petit-', 'neveu', 'du ', 'du '))->sibling()->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $compound($n, 'petit-', 'neveu par alliance', 'du ', 'du '))->married()->spouse()->sibling()->descendant()->male(),
            // Cousins (based on canon law)
            Relationship::dynamic(static fn (int $up, int $down) => $cousin($up, $down, 'F'))->cousin()->female(),
            Relationship::dynamic(static fn (int $up, int $down) => $cousin($up, $down, 'M'))->cousin()->male(),

        ];
    }
}
