'use strict';

import { requireDatasetValue, requireElement } from '../dom';
import { initializeFeaturePanelMap } from '../map-panel';
import { parseJsonDataset, stopNestedLinkClicks, zoomToFeaturePopup } from '../map-sidebar';

/**
 * Initialize places tab map panels.
 */
export function initializePlacesTabMapPage(root) {
  root.querySelectorAll('[data-wt-places-tab-map]').forEach((container) => {
      if (!(container instanceof HTMLElement)) {
        throw new Error('Places tab map container must be an HTML element.');
      }

      const mapElement = requireElement(container, '[data-wt-map-canvas]', HTMLDivElement, 'places tab map canvas');
      const sidebar = requireElement(container, '[data-wt-map-sidebar]', HTMLElement, 'places tab map sidebar');
      const leafletConfig = parseJsonDataset(requireDatasetValue(container, 'wtLeafletConfig', 'places tab leaflet config'), 'places tab leaflet config');
      const geoJsonData = parseJsonDataset(requireDatasetValue(container, 'wtMapData', 'places tab map data'), 'places tab map data');
      const nothingToShow = requireDatasetValue(container, 'wtNothingToShow', 'places tab empty state');

      if (mapElement.id === '') {
        throw new Error('Places tab map canvas must have an id attribute.');
      }

      const { map, markers, hasFeatures } = initializeFeaturePanelMap({
        mapElementId: mapElement.id,
        leafletConfig,
        sidebar,
        geoJsonData,
        emptySidebarHtml: '<div class="bg-info text-white text-center">' + nothingToShow + '</div>',
        createMarker: (feature, latlng) => new L.Marker(latlng, {
          icon: L.BeautifyIcon.icon({
            icon: feature.properties.icon.name,
            borderColor: 'transparent',
            backgroundColor: feature.properties.icon.color,
            iconShape: 'marker',
            textColor: 'white',
          }),
          title: feature.properties.tooltip,
          id: feature.id,
        }),
        onEachFeature: (feature, layer, context) => {
          layer.bindPopup(feature.properties.summary);
          context.sidebar.insertAdjacentHTML('beforeend', `<li class="mb-1 wt-page-options-value" data-wt-feature-id="${feature.id}">${feature.properties.summary}</li>`);
        },
      });

      if (!hasFeatures) {
        return;
      }

      sidebar.querySelectorAll('[data-wt-feature-id]').forEach((item) => {
        if (!(item instanceof HTMLElement)) {
          return;
        }

        const featureId = Number.parseInt(item.dataset.wtFeatureId ?? '', 10);

        if (!Number.isInteger(featureId)) {
          return;
        }

        item.addEventListener('click', () => {
          map.closePopup();
          zoomToFeaturePopup(markers, featureId);
        });

        stopNestedLinkClicks(item);
      });
    });
}
