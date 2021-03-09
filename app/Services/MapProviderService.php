<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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

use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Site;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Collection;

use function array_combine;
use function explode;
use function unserialize;

/**
 * A collection of functions used by the Mapping facilities
 */
class MapProviderService
{

    // Location of files to import
    public const PROVIDER_FOLDER  = 'map-providers/';
    // System default provider and style
    public const SYSTEM_DEFAULT = 'openstreetmap.mapnik';

    /**
     * Return a collection of providers
     *
     * @param string $type
     *
     * @return Collection<string,string>
     */
    public function providers($type = 'all'): collection
    {
        $query = DB::table('map_names')
            ->whereNull('provider_id');
        switch ($type) {
            case 'added':
                $query->where('key_name', '<>', $this->systemDefault()->get('provider'));
                break;
            case 'enabled':
                $inner = DB::table(function ($qry) {
                    $qry->selectRaw("*, key_name || '-enabled' as enabled")
                        ->from('map_names');
                });
                $query->from($inner)
                    ->leftJoin('site_setting', 'enabled', '=', 'setting_name')
                    ->whereNull('provider_id')
                    ->where(function ($qry) {
                        $qry->whereNull('setting_name') // Openstreetmaap
                            ->orWhere('setting_value', '=', '1');
                    });
                break;
            default:
        }
        $query->orderBy('id');

        return $query->pluck('display_name', 'key_name');
    }

    /**
     * Return all styles for a specific provider
     *
     * @param string $provider
     * @return Collection<string>
     */
    public function styles(string $provider): Collection
    {
        return DB::table('map_names as n1')
            ->join('map_names as n2', 'n2.provider_id', '=', 'n1.id')
            ->where('n1.key_name', '=', $provider)
            ->orderBy('n1.id')
            ->pluck('n2.display_name', 'n2.key_name');
    }

    /**
     * Return the system default provider & style
     *
     * @return Collection<string>
     */
    public function systemDefault(): Collection
    {
        return collect(array_combine(['provider', 'style'], explode('.', self::SYSTEM_DEFAULT)));
    }

    /**
     * Return the currently saved default provider & style
     *
     * @return Collection<string>
     */
    public function defaultProvider(): Collection
    {
        $pref    = explode('.', Site::getPreference('default-map-provider', self::SYSTEM_DEFAULT));
        $default = collect(array_combine(['provider', 'style'], $pref));
        $exists  = $this->providers()->has($default->get('provider'));
        if (!$exists || !(bool) Site::getPreference($default->get('provider') . '-enabled')) {
            $default = $this->systemDefault();
        }

        return $default;
    }

    /**
     * Return the values of all user provided parameters
     * for a specified provider
     *
     * @param string $provider
     * @return Collection<string>
     */
    public function userParameters($provider): Collection
    {
        return DB::table('map_parameters as p1')
            ->join('map_names as n1', 'p1.parent_id', '=', 'n1.id')
            ->where('n1.key_name', '=', $provider)
            ->where('p1.type', '=', 'user')
            ->pluck('p1.parameter_value', 'p1.parameter_name')
            ->map(function ($value) {
                return unserialize($value);
            });
    }

    /**
     * Provide an array of layers to modules that draw a map
     *
     * @return array<mixed>
     */
    public function providerLayers(): array
    {
        $self = $this;

        return Registry::cache()->array()->remember('map-layers', static function () use ($self) {
            $layers = [];    // connection data for each provider/style
            foreach ($self->providers('enabled') as $key => $provider) {
                $layers[] = [
                    'label'     => "<span class='font-weight-bold'>" . $provider . "</span>",
                    'collapsed' => 'true',
                    'children'  => $self->styleParameters($key),
                ];
            }

            return $layers;
        });
    }

    /**
     * Return indexes to the default provider & style layers
     *
     * @return array<string,int>
     */
    public function defaultLayers(): array
    {
        $self = $this;

        return Registry::cache()->array()->remember('default-map-layers', static function () use ($self) {
            $default   = $self->defaultProvider();
            $providers = $self->providers('enabled');
            $styles    = $self->styles($default->get('provider'));

            return [
                'provider' => (int) $providers->keys()->search($default->get('provider')),
                'style'    => (int) $styles->keys()->search($default->get('style'))
            ];
        });
    }

    /**
     * Return layer data for all styles of the selected provider
     *
     * @param string $provider
     *
     * @return array<mixed>
     */
    private function styleParameters($provider): array
    {
        $data = [];
        foreach ($this->styles($provider) as $key => $name) {
            $parameters = DB::table('map_parameters as p1')
                ->join('map_names as n1', 'p1.parent_id', '=', 'n1.id')
                ->whereIn('n1.key_name', [$provider, $key])
                ->pluck('p1.parameter_value', 'p1.parameter_name')
                ->map(function ($value) {
                    return unserialize($value);
                });

            $url = $parameters->get('url');
            $parameters->forget('url');
            $parameters->forget('help');

            $data[] = [
                'label'   => '<span class="px-1">' . $name . '</span>',
                'layer'   => [
                    'title'      => $name,
                    'url'        => $url,
                    'parameters' => $parameters,
                ]

            ];
        }

        return $data;
    }
}
