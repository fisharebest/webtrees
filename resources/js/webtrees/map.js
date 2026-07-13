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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

import { i18n } from './i18n';

/**
 * Create a LeafletJS map from a list of providers/layers.
 *
 * @param {string} id
 * @param {object} config
 * @param {function} resetCallback
 *
 * @returns {Map}
 */
export function buildLeafletJsMap(id, config, resetCallback) {
  const zoomControl = new L.control.zoom({
    zoomInTitle: i18n.get('Zoom in'),
    zoomoutTitle: i18n.get('Zoom out'),
  });

  const resetControl = L.Control.extend({
    options: {
      position: 'topleft',
    },
    onAdd: () => {
      const container = L.DomUtil.create('div', 'leaflet-bar leaflet-control leaflet-control-custom');
      const anchor = L.DomUtil.create('a', 'leaflet-control-reset', container);

      anchor.href = '#';
      anchor.setAttribute('aria-label', i18n.get('Reload map')); // Firefox does not yet support element.ariaLabel
      anchor.title = i18n.get('Reload map');
      anchor.setAttribute('role', 'button');
      anchor.innerHTML = config.icons.reset;
      anchor.onclick = resetCallback;

      return container;
    },
  });

  const fullscreenControl = L.Control.extend({
    options: {
      position: 'topleft',
    },
    onAdd: (map) => {
      const container = L.DomUtil.create('div', 'leaflet-bar leaflet-control leaflet-control-custom');
      const anchor = L.DomUtil.create('a', 'leaflet-control-fullscreen', container);

      anchor.href = '#';
      anchor.setAttribute('role', 'button');
      anchor.dataset.wtFullscreen = '.wt-fullscreen-container';
      anchor.innerHTML = config.icons.fullScreen;

      return container;
    },
  });

  const preferredLayer = localStorage.getItem('map_default_layer');
  let defaultLayer = null;

  for (let [, provider] of Object.entries(config.mapProviders)) {
    for (let [, child] of Object.entries(provider.children)) {
      child.layer = L.tileLayer(child.url, child);

      if (preferredLayer === child.localName) {
        defaultLayer = child.layer;
      }

      if (defaultLayer === null && provider['default'] && child['default']) {
        defaultLayer = child.layer;
      }
    }
  }

  if (defaultLayer === null) {
    console.log('No default map layer defined - using the first one.');
    defaultLayer = config.mapProviders[0].children[0].layer;
  }

  // Create the map with all controls and layers
  return L.map(id, {
    zoomControl: false,
  })
    .addControl(zoomControl)
    .addControl(new fullscreenControl())
    .addControl(new resetControl())
    .addLayer(defaultLayer)
    .addControl(L.control.layers.tree(config.mapProviders, null, {
      closedSymbol: config.icons.expand,
      openedSymbol: config.icons.collapse,
    }))
    .on('baselayerchange', (layer) => {
      localStorage.setItem('map_default_layer', layer.layer.options.localName);
    });
}
