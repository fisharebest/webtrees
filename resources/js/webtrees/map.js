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
  if (!config || typeof config !== 'object' || !config.icons || typeof config.icons !== 'object') {
    throw new Error('Invalid map configuration: missing icons.');
  }

  const requiredIcons = ['reset', 'fullScreen', 'expand', 'collapse'];
  requiredIcons.forEach((name) => {
    if (typeof config.icons[name] !== 'string' || config.icons[name] === '') {
      throw new Error('Invalid map configuration: missing icon "' + name + '".');
    }
  });

  const zoomControl = new L.control.zoom({
    zoomInTitle: i18n.get('Zoom in'),
    zoomOutTitle: i18n.get('Zoom out'),
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
      anchor.addEventListener('click', (event) => {
        event.preventDefault();
        resetCallback(event);
      });

      return container;
    },
  });

  const fullscreenControl = L.Control.extend({
    options: {
      position: 'topleft',
    },
    onAdd: () => {
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

  if (!config || typeof config !== 'object' || !config.mapProviders || typeof config.mapProviders !== 'object') {
    throw new Error('Invalid map configuration: missing mapProviders.');
  }

  for (let [providerName, provider] of Object.entries(config.mapProviders)) {
    if (!provider || typeof provider !== 'object' || !provider.children || typeof provider.children !== 'object') {
      throw new Error('Invalid map provider configuration: missing children for provider "' + providerName + '".');
    }

    for (let [childName, child] of Object.entries(provider.children)) {
      if (!child || typeof child !== 'object' || typeof child.url !== 'string' || typeof child.localName !== 'string') {
        throw new Error('Invalid map layer configuration for provider "' + providerName + '", layer "' + childName + '".');
      }

      child.layer = L.tileLayer(child.url, child);

      if (preferredLayer === child.localName) {
        defaultLayer = child.layer;
      }

      if (defaultLayer === null && provider.default && child.default) {
        defaultLayer = child.layer;
      }
    }
  }

  if (defaultLayer === null) {
    throw new Error('No default map layer configured.');
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
