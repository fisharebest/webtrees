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

namespace Fisharebest\Webtrees;

use Closure;
use Fisharebest\Webtrees\Elements\PedigreeLinkageType;
use Fisharebest\Webtrees\Enums\AccessLevel;
use Fisharebest\Webtrees\Enums\Relation;
use Fisharebest\Webtrees\Enums\Sex;

use function abs;
use function array_slice;
use function count;
use function in_array;
use function intdiv;
use function min;

class Relationship
{
    // Generates a name from the matched relationship.
    private Closure $callback;

    /** @var array<Closure> List of rules that need to match */
    private array $matchers;

    private function __construct(Closure $callback)
    {
        $this->callback = $callback;
        $this->matchers = [];
    }

    /**
     * Allow fluent constructor.
     */
    public static function fixed(string $nominative, string $genitive, string $genitiveFemale = ''): Relationship
    {
        return new self(fn () => $genitiveFemale !== ''
            ? [$nominative, $genitive, $genitiveFemale]
            : [$nominative, $genitive]);
    }

    /**
     * Allow fluent constructor.
     */
    public static function dynamic(Closure $callback): Relationship
    {
        return new self($callback);
    }

    /**
     * Does this relationship match the pattern?
     *
     * @param array<Individual|Family> $nodes
     * @param array<Relation>          $patterns
     *
     * @return array<string>|null [nominative, genitive] or null
     */
    public function match(array $nodes, array $patterns): array|null
    {
        $captures = [];

        foreach ($this->matchers as $matcher) {
            if (!$matcher($nodes, $patterns, $captures)) {
                return null;
            }
        }

        if ($patterns === []) {
            return ($this->callback)(...$captures);
        }

        return null;
    }

    public function adopted(): Relationship
    {
        $this->matchers[] = static fn (array $nodes): bool => count($nodes) > 2 && $nodes[2]
                ->facts(['FAMC'], false, AccessLevel::Hidden)
                ->contains(fn (Fact $fact): bool => $fact->value() === '@' . $nodes[1]->xref() . '@' && $fact->attribute('PEDI') === PedigreeLinkageType::VALUE_ADOPTED);

        return $this;
    }

    public function adoptive(): Relationship
    {
        $this->matchers[] = static fn (array $nodes): bool => $nodes[0]
            ->facts(['FAMC'], false, AccessLevel::Hidden)
            ->contains(fn (Fact $fact): bool => $fact->value() === '@' . $nodes[1]->xref() . '@' && $fact->attribute('PEDI') === PedigreeLinkageType::VALUE_ADOPTED);

        return $this;
    }

    public function brother(): Relationship
    {
        return $this->relation([Relation::Brother]);
    }

    /**
     * Match the next relationship in the path.
     *
     * @param array<Relation> $relationships
     */
    protected function relation(array $relationships): Relationship
    {
        $this->matchers[] = static function (array &$nodes, array &$patterns) use ($relationships): bool {
            if (in_array($patterns[0] ?? '', $relationships, true)) {
                $nodes    = array_slice($nodes, 2);
                $patterns = array_slice($patterns, 1);

                return true;
            }

            return false;
        };

        return $this;
    }

    /**
     * The number of ancestors may be different to the number of descendants
     */
    public function cousin(): Relationship
    {
        return $this->ancestor()->sibling()->descendant();
    }

    public function descendant(): Relationship
    {
        return $this->repeatedRelationship([Relation::Daughter, Relation::Son, Relation::Child]);
    }

    /**
     * Match a repeated number of the same type of component
     *
     * @param array<Relation> $relationships
     */
    protected function repeatedRelationship(array $relationships): Relationship
    {
        $this->matchers[] = static function (array &$nodes, array &$patterns, array &$captures) use ($relationships): bool {
            $limit = min(intdiv(count($nodes), 2), count($patterns));

            $generations = 0;

            while ($generations < $limit && in_array($patterns[$generations], $relationships, true)) {
                ++$generations;
            }

            if ($generations > 0) {
                $nodes      = array_slice($nodes, 2 * $generations);
                $patterns   = array_slice($patterns, $generations);
                $captures[] = $generations;

                return true;
            }

            return false;
        };

        return $this;
    }

    public function sibling(): Relationship
    {
        return $this->relation([Relation::Sister, Relation::Brother, Relation::Sibling]);
    }

    public function ancestor(): Relationship
    {
        return $this->repeatedRelationship([Relation::Mother, Relation::Father, Relation::Parent]);
    }

    public function child(): Relationship
    {
        return $this->relation([Relation::Daughter, Relation::Son, Relation::Child]);
    }

    public function daughter(): Relationship
    {
        return $this->relation([Relation::Daughter]);
    }

    public function divorced(): Relationship
    {
        return $this->marriageStatus('DIV');
    }

    /**
     * Match a marriage status
     */
    protected function marriageStatus(string $status): Relationship
    {
        $this->matchers[] = static function (array $nodes) use ($status): bool {
            $family = $nodes[1] ?? null;

            if ($family instanceof Family) {
                $fact = $family->facts(['ENGA', 'MARR', 'DIV', 'ANUL'], true, AccessLevel::Hidden)->last();

                if ($fact instanceof Fact) {
                    switch ($status) {
                        case 'MARR':
                            return $fact->tag() === 'FAM:MARR';

                        case 'DIV':
                            return $fact->tag() === 'FAM:DIV' || $fact->tag() === 'FAM:ANUL';

                        case 'ENGA':
                            return $fact->tag() === 'FAM:ENGA';
                    }
                }
            }

            return false;
        };

        return $this;
    }

    public function engaged(): Relationship
    {
        return $this->marriageStatus('ENGA');
    }

    public function father(): Relationship
    {
        return $this->relation([Relation::Father]);
    }

    public function female(): Relationship
    {
        return $this->sex(Sex::Female);
    }

    /**
     * Match the sex of the current individual
     */
    protected function sex(Sex $sex): Relationship
    {
        $this->matchers[] = static fn (array $nodes): bool => $nodes[0]->sex() === $sex;

        return $this;
    }

    public function fostered(): Relationship
    {
        $this->matchers[] = static fn (array $nodes): bool => count($nodes) > 2 && $nodes[2]
                ->facts(['FAMC'], false, AccessLevel::Hidden)
                ->contains(fn (Fact $fact): bool => $fact->value() === '@' . $nodes[1]->xref() . '@' && $fact->attribute('PEDI') === PedigreeLinkageType::VALUE_FOSTER);

        return $this;
    }

    public function fostering(): Relationship
    {
        $this->matchers[] = static fn (array $nodes): bool => $nodes[0]
            ->facts(['FAMC'], false, AccessLevel::Hidden)
            ->contains(fn (Fact $fact): bool => $fact->value() === '@' . $nodes[1]->xref() . '@' && $fact->attribute('PEDI') === PedigreeLinkageType::VALUE_FOSTER);

        return $this;
    }

    public function husband(): Relationship
    {
        return $this->married()->relation([Relation::Husband]);
    }

    public function married(): Relationship
    {
        return $this->marriageStatus('MARR');
    }

    public function male(): Relationship
    {
        return $this->sex(Sex::Male);
    }

    /**
     * Match when the first individual in the path is female.
     */
    public function selfFemale(): Relationship
    {
        $this->matchers[] = static fn (array $nodes): bool => $nodes[0]->sex() === Sex::Female;

        return $this;
    }

    public function mother(): Relationship
    {
        return $this->relation([Relation::Mother]);
    }

    public function older(): Relationship
    {
        $this->matchers[] = static function (array $nodes): bool {
            $date1 = $nodes[0]->facts(['BIRT'], false, AccessLevel::Hidden)->map(fn (Fact $fact): Date => $fact->date())->first() ?? new Date('');
            $date2 = $nodes[count($nodes) - 1]->facts(['BIRT'], false, AccessLevel::Hidden)->map(fn (Fact $fact): Date => $fact->date())->first() ?? new Date('');

            return Date::compare($date1, $date2) > 0;
        };

        return $this;
    }

    public function parent(): Relationship
    {
        return $this->relation([Relation::Mother, Relation::Father, Relation::Parent]);
    }

    public function sister(): Relationship
    {
        return $this->relation([Relation::Sister]);
    }

    public function son(): Relationship
    {
        return $this->relation([Relation::Son]);
    }

    public function spouse(): Relationship
    {
        return $this->married()->partner();
    }

    public function partner(): Relationship
    {
        return $this->relation([Relation::Wife, Relation::Husband, Relation::Spouse]);
    }

    /**
     * The number of ancestors must be the same as the number of descendants
     */
    public function symmetricCousin(): Relationship
    {
        $this->matchers[] = static function (array &$nodes, array &$patterns, array &$captures): bool {
            $count = count($patterns);

            $n = 0;

            // Ancestors
            while ($n < $count && in_array($patterns[$n], [Relation::Mother, Relation::Father, Relation::Parent], true)) {
                $n++;
            }

            // No ancestors?  Not enough of the path left for descendants?
            if ($n === 0 || $n * 2 + 1 !== $count) {
                return false;
            }

            // Siblings
            if (!in_array($patterns[$n], [Relation::Sister, Relation::Brother, Relation::Sibling], true)) {
                return false;
            }

            // Descendants
            for ($descendants = $n + 1; $descendants < $count; ++$descendants) {
                if (!in_array($patterns[$descendants], [Relation::Daughter, Relation::Son, Relation::Child], true)) {
                    return false;
                }
            }

            $nodes      = array_slice($nodes, 2 * (2 * $n + 1));
            $patterns   = [];
            $captures[] = $n;

            return true;
        };

        return $this;
    }

    public function multiple(): Relationship
    {
        $this->matchers[] = static function (array $nodes): bool {
            $date1 = $nodes[0]->facts(['BIRT'], false, AccessLevel::Hidden)->map(fn (Fact $fact): Date => $fact->date())->first() ?? new Date('');
            $date2 = $nodes[count($nodes) - 1]->facts(['BIRT'], false, AccessLevel::Hidden)->map(fn (Fact $fact): Date => $fact->date())->first() ?? new Date('');

            return
                $date1->isOK() &&
                $date2->isOK() &&
                abs($date1->julianDay() - $date2->julianDay()) < 2 &&
                $date1->minimumDate()->day > 0 &&
                $date2->minimumDate()->day > 0;
        };

        return $this;
    }

    public function wife(): Relationship
    {
        return $this->married()->relation([Relation::Wife]);
    }

    public function younger(): Relationship
    {
        $this->matchers[] = static function (array $nodes): bool {
            $date1 = $nodes[0]
                ->facts(['BIRT'], false, AccessLevel::Hidden)
                ->map(fn (Fact $fact): Date => $fact->date())
                ->first() ?? new Date('');

            $date2 = $nodes[count($nodes) - 1]
                ->facts(['BIRT'], false, AccessLevel::Hidden)
                ->map(fn (Fact $fact): Date => $fact->date())
                ->first() ?? new Date('');

            return Date::compare($date1, $date2) < 0;
        };

        return $this;
    }
}
