'use strict';

import { buildLeafletJsMap } from './map';

/**
 * Create a clustered Leaflet map with optional GeoJSON rendering and sidebar popup synchronization.
 *
 * @param {object} options
 * @param {string} options.mapElementId
 * @param {object} options.leafletConfig
 * @param {HTMLElement} options.sidebar
 * @param {object} options.geoJsonData
 * @param {((feature: any, latlng: any) => L.Marker)} options.createMarker
 * @param {(feature: any, layer: L.Layer, context: {map: L.Map, markers: L.MarkerClusterGroup, sidebar: HTMLElement}) => void} options.onEachFeature
 * @param {string|null} [options.sidebarHtml]
 * @param {string|null} [options.emptySidebarHtml]
 * @param {Array<Array<number>>|null} [options.fitBounds]
 * @param {'world'|'none'} [options.resetWithoutMarkers]
 * @param {(featureId: number) => string} [options.highlightSelector]
 * @param {string} [options.clearHighlightSelector]
 * @returns {{map: L.Map, markers: L.MarkerClusterGroup, hasFeatures: boolean}}
 */
export function initializeFeaturePanelMap(options) {
  const {
    mapElementId,
    leafletConfig,
    sidebar,
    geoJsonData,
    createMarker,
    onEachFeature,
    sidebarHtml = null,
    emptySidebarHtml = null,
    fitBounds = null,
    resetWithoutMarkers = 'world',
    highlightSelector = (featureId) => '[data-wt-feature-id="' + featureId + '"]',
    clearHighlightSelector = '[data-wt-feature-id]',
  } = options;

  const scrollOptions = {
    behavior: 'smooth',
    block: 'nearest',
    inline: 'start',
  };

  const markers = L.markerClusterGroup({
    showCoverageOnHover: false,
  });

  const map = buildLeafletJsMap(mapElementId, leafletConfig, (event) => {
    event.preventDefault();

    if (markers.getLayers().length > 0) {
      map.flyToBounds(markers.getBounds(), { padding: [50, 30], maxZoom: 15 });
    } else if (resetWithoutMarkers === 'world') {
      map.fitWorld();
    }
  });

  if (sidebarHtml !== null) {
    sidebar.innerHTML = sidebarHtml;
  }

  const hasFeatures = Array.isArray(geoJsonData.features) && geoJsonData.features.length > 0;

  if (!hasFeatures) {
    if (emptySidebarHtml !== null) {
      sidebar.innerHTML = emptySidebarHtml;
    }

    if (fitBounds !== null) {
      map.fitBounds(fitBounds, { padding: [50, 30] });
    } else {
      map.fitWorld();
    }

    return { map, markers, hasFeatures: false };
  }

  const geoJsonLayer = L.geoJson(geoJsonData, {
    pointToLayer: (feature, latlng) => {
      const marker = createMarker(feature, latlng);

      return marker
        .on('popupopen', (popupEvent) => {
          const item = sidebar.querySelector(highlightSelector(popupEvent.target.feature.id));

          if (item instanceof HTMLElement) {
            item.classList.add('messagebox');
            item.scrollIntoView(scrollOptions);
          }
        })
        .on('popupclose', () => {
          sidebar.querySelectorAll(clearHighlightSelector).forEach((item) => item.classList.remove('messagebox'));
        });
    },
    onEachFeature: (feature, layer) => {
      onEachFeature(feature, layer, { map, markers, sidebar });
    },
  });

  markers.addLayer(geoJsonLayer);
  map.addLayer(markers);

  if (fitBounds !== null) {
    map.fitBounds(fitBounds, { padding: [50, 30] });
  } else {
    map.fitBounds(markers.getBounds(), { padding: [50, 30], maxZoom: 15 });
  }

  return { map, markers, hasFeatures: true };
}

