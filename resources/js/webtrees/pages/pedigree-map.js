'use strict';

import { requireDatasetValue, requireElement } from '../dom';
import { initializeFeaturePanelMap } from '../map-panel';
import { parseJsonDataset, zoomToFeaturePopup } from '../map-sidebar';

/**
 * Initialize pedigree map pages.
 */
export function initializePedigreeMapPage(root) {
  root.querySelectorAll('[data-wt-pedigree-map]').forEach((container) => {
      if (!(container instanceof HTMLElement)) {
        throw new Error('Pedigree map container must be an HTML element.');
      }

      const mapElement = requireElement(container, '[data-wt-map-canvas]', HTMLDivElement, 'pedigree map canvas');
      const sidebar = requireElement(container, '[data-wt-map-sidebar]', HTMLElement, 'pedigree map sidebar');
      const leafletConfig = parseJsonDataset(requireDatasetValue(container, 'wtLeafletConfig', 'pedigree map leaflet config'), 'pedigree map leaflet config');
      const geoJsonData = parseJsonDataset(requireDatasetValue(container, 'wtMapData', 'pedigree map data'), 'pedigree map data');
      const nothingToShow = requireDatasetValue(container, 'wtNothingToShow', 'pedigree map empty state');

      if (mapElement.id === '') {
        throw new Error('Pedigree map canvas must have an id attribute.');
      }

      const { map, markers } = initializeFeaturePanelMap({
        mapElementId: mapElement.id,
        leafletConfig,
        sidebar,
        geoJsonData,
        emptySidebarHtml: '<div class="bg-info text-white text-center">' + nothingToShow + '</div>',
        createMarker: (feature, latlng) => new L.Marker(latlng, {
          icon: L.BeautifyIcon.icon({
            icon: 'bullseye fas',
            borderColor: 'transparent',
            backgroundColor: feature.properties.iconcolor,
            iconShape: 'marker',
            textColor: 'white',
          }),
          title: feature.properties.tooltip,
          id: feature.id,
        }),
        onEachFeature: (feature, layer, context) => {
          if (feature.properties.polyline) {
            context.markers.addLayer(L.polyline(feature.properties.polyline.points, feature.properties.polyline.options));
          }

          layer.bindPopup(feature.properties.summary);
          context.sidebar.insertAdjacentHTML('beforeend', `<li class="gchart p-1 mb-1" data-wt-feature-id="${feature.id}">${feature.properties.summary}</li>`);
        },
      });

      sidebar.addEventListener('click', (event) => {
        const target = event.target instanceof Element ? event.target.closest('[data-wt-sosa]') : null;

        if (!(target instanceof HTMLElement)) {
          return;
        }

        event.preventDefault();
        map.closePopup();

        const featureId = Number.parseInt(target.dataset.wtSosa ?? '', 10);

        if (!Number.isInteger(featureId)) {
          return;
        }

        zoomToFeaturePopup(markers, featureId);
        return false;
      });
    });
}
