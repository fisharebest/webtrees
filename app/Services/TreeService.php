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

namespace Fisharebest\Webtrees\Services;

use DomainException;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\GedcomFilters\GedcomEncodingFilter;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Psr\Http\Message\StreamInterface;

use function fclose;
use function feof;
use function fread;
use function max;
use function stream_filter_append;
use function strrpos;
use function substr;

use const STREAM_FILTER_READ;

/**
 * Tree management and queries.
 */
class TreeService
{
    // The most likely surname tradition for a given language.
    private const array DEFAULT_SURNAME_TRADITIONS = [
        'es'    => 'spanish',
        'is'    => 'icelandic',
        'lt'    => 'lithuanian',
        'pl'    => 'polish',
        'pt'    => 'portuguese',
        'pt-BR' => 'portuguese',
    ];

    public function __construct(
        private readonly GedcomImportService $gedcom_import_service,
    ) {
    }

    /**
     * All the trees that the current user has permission to access.
     *
     * @return Collection<array-key,Tree>
     */
    public function all(): Collection
    {
        return Registry::cache()->array()->remember('all-trees', static function (): Collection {
            // All trees
            $query = DB::table('gedcom')
                ->where('gedcom.gedcom_id', '>', 0)
                ->when(!Auth::isAdmin(), function (Builder $query): void {
                    $query->leftJoin('user_gedcom_setting', static function (JoinClause $join): void {
                        $join
                            ->on('user_gedcom_setting.gedcom_id', '=', 'gedcom.gedcom_id')
                            ->where('user_id', '=', Auth::id())
                            ->where('setting_name', '=', UserInterface::PREF_TREE_ROLE);
                    });

                    $query->where(static function (Builder $query): void {
                        $query
                            // Managers
                            ->where('setting_value', '=', UserInterface::ROLE_MANAGER)
                            // Members
                            ->orWhere(static function (Builder $query): void {
                                $query
                                    ->where('imported', '=', 1)
                                    ->where('private', '=', 1)
                                    ->where('setting_value', '<>', UserInterface::ROLE_VISITOR);
                            })
                            // Public trees
                            ->orWhere(static function (Builder $query): void {
                                $query
                                    ->where('imported', '=', 1)
                                    ->where('private', '=', 0);
                            });
                    });
                })
                ->select(['gedcom.*'])
                ->orderBy('gedcom.sort_order')
                ->orderBy('gedcom.title');

            // TODO - do we need the array keys, or would a list of trees be sufficient?
            return $query
                ->get()
                ->map(Tree::fromDB(...))
                ->mapWithKeys(static fn (Tree $tree): array => [$tree->name() => $tree]);
        });
    }

    /**
     * Find a tree by its ID.
     *
     * @param int $id
     *
     * @return Tree
     */
    public function find(int $id): Tree
    {
        $tree = $this->all()->first(static fn (Tree $tree): bool => $tree->id() === $id);

        if ($tree instanceof Tree) {
            return $tree;
        }

        throw new DomainException('Call to find() with an invalid id: ' . $id);
    }

    /**
     * @return array<array-key,string>
     */
    public function titles(): array
    {
        return $this->all()
            ->mapWithKeys(static fn (Tree $tree): array => [$tree->name() => $tree->title()])
            ->all();
    }

    public function create(string $name, string $title): Tree
    {
        DB::table('gedcom')->insert([
            'contact_user_id' => Auth::id(),
            'gedcom_filename' => $name . '.ged',
            'gedcom_name'     => $name,
            'support_user_id' => Auth::id(),
            'title'           => $title,
        ]);

        $tree = DB::table('gedcom')
            ->where('gedcom_id', '=', DB::lastInsertId())
            ->get()
            ->map(Tree::fromDB(...))
            ->first();

        // Set preferences from the default tree
        DB::query()->from('gedcom_setting')->insertUsing(
            ['gedcom_id', 'setting_name', 'setting_value'],
            static function (Builder $query) use ($tree): void {
                $query
                    ->select([new Expression($tree->id()), 'setting_name', 'setting_value'])
                    ->from('gedcom_setting')
                    ->where('gedcom_id', '=', -1);
            }
        );

        DB::query()->from('default_resn')->insertUsing(
            ['gedcom_id', 'tag_type', 'resn'],
            static function (Builder $query) use ($tree): void {
                $query
                    ->select([new Expression($tree->id()), 'tag_type', 'resn'])
                    ->from('default_resn')
                    ->where('gedcom_id', '=', -1);
            }
        );

        // Gedcom and privacy settings
        $tree->setPreference('SURNAME_TRADITION', self::DEFAULT_SURNAME_TRADITIONS[I18N::languageTag()] ?? 'paternal');

        // A tree needs at least one record.
        $head = "0 HEAD\n1 SOUR webtrees\n1 DEST webtrees\n1 GEDC\n2 VERS 5.5.1\n2 FORM LINEAGE-LINKED\n1 CHAR UTF-8";
        $this->gedcom_import_service->importRecord($head, $tree, true);

        // I18N: This should be a common/default/placeholder name of an individual. Put slashes around the surname.
        $name = I18N::translate('John /DOE/');
        $note = I18N::translate('Edit this individual and replace their details with your own.');
        $indi = "0 @X1@ INDI\n1 NAME " . $name . "\n1 SEX M\n1 BIRT\n2 DATE 01 JAN 1850\n2 NOTE " . $note;
        $this->gedcom_import_service->importRecord($indi, $tree, true);

        return $tree;
    }

    /**
     * Import data from a gedcom file into this tree.
     *
     * @param Tree            $tree
     * @param StreamInterface $stream   The GEDCOM file.
     * @param string          $filename The preferred filename, for export/download.
     * @param string          $encoding Override the encoding specified in the header.
     *
     * @return void
     */
    public function importGedcomFile(Tree $tree, StreamInterface $stream, string $filename, string $encoding): void
    {
        // Read the file in blocks of roughly 64K. Ensure that each block
        // contains complete gedcom records. This will ensure we don’t split
        // multibyte characters, as well as simplifying the code to import
        // each block.

        $file_data = '';

        DB::table('gedcom')->where('gedcom_id', '=', $tree->id())->update([
            'gedcom_filename' => $filename,
            'imported' => 0,
        ]);

        DB::table('gedcom_chunk')->where('gedcom_id', '=', $tree->id())->delete();

        $stream = $stream->detach();

        // Convert to UTF-8.
        stream_filter_append($stream, GedcomEncodingFilter::class, STREAM_FILTER_READ, ['src_encoding' => $encoding]);

        while (!feof($stream)) {
            $file_data .= fread($stream, 65536);
            $eol_pos = max((int) strrpos($file_data, "\r0"), (int) strrpos($file_data, "\n0"));

            if ($eol_pos > 0) {
                DB::table('gedcom_chunk')->insert([
                    'gedcom_id'  => $tree->id(),
                    'chunk_data' => substr($file_data, 0, $eol_pos + 1),
                ]);

                $file_data = substr($file_data, $eol_pos + 1);
            }
        }

        DB::table('gedcom_chunk')->insert([
            'gedcom_id'  => $tree->id(),
            'chunk_data' => $file_data,
        ]);

        fclose($stream);
    }

    /**
     * @param Tree $tree
     */
    public function delete(Tree $tree): void
    {
        // If this is the default tree, then unset it
        if (Site::getPreference('DEFAULT_GEDCOM') === $tree->name()) {
            Site::setPreference('DEFAULT_GEDCOM', '');
        }

        DB::table('gedcom_chunk')->where('gedcom_id', '=', $tree->id())->delete();
        DB::table('individuals')->where('i_file', '=', $tree->id())->delete();
        DB::table('families')->where('f_file', '=', $tree->id())->delete();
        DB::table('sources')->where('s_file', '=', $tree->id())->delete();
        DB::table('other')->where('o_file', '=', $tree->id())->delete();
        DB::table('places')->where('p_file', '=', $tree->id())->delete();
        DB::table('placelinks')->where('pl_file', '=', $tree->id())->delete();
        DB::table('name')->where('n_file', '=', $tree->id())->delete();
        DB::table('dates')->where('d_file', '=', $tree->id())->delete();
        DB::table('change')->where('gedcom_id', '=', $tree->id())->delete();
        DB::table('link')->where('l_file', '=', $tree->id())->delete();
        DB::table('media_file')->where('m_file', '=', $tree->id())->delete();
        DB::table('media')->where('m_file', '=', $tree->id())->delete();
        DB::table('block_setting')
            ->join('block', 'block.block_id', '=', 'block_setting.block_id')
            ->where('gedcom_id', '=', $tree->id())
            ->delete();
        DB::table('block')->where('gedcom_id', '=', $tree->id())->delete();
        DB::table('user_gedcom_setting')->where('gedcom_id', '=', $tree->id())->delete();
        DB::table('gedcom_setting')->where('gedcom_id', '=', $tree->id())->delete();
        DB::table('module_privacy')->where('gedcom_id', '=', $tree->id())->delete();
        DB::table('hit_counter')->where('gedcom_id', '=', $tree->id())->delete();
        DB::table('default_resn')->where('gedcom_id', '=', $tree->id())->delete();
        DB::table('log')->where('gedcom_id', '=', $tree->id())->delete();
        DB::table('gedcom')->where('gedcom_id', '=', $tree->id())->delete();
    }

    /**
     * Generate a unique name for a new tree.
     *
     * @return string
     */
    public function uniqueTreeName(): string
    {
        $name   = 'tree';
        $number = 1;

        while ($this->all()->get($name . $number) instanceof Tree) {
            $number++;
        }

        return $name . $number;
    }
}
