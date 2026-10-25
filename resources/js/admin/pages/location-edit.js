'use strict';

import { buildLeafletJsMap, parseJsonDataset, requireDatasetValue, requireElement } from '../../webtrees/index';

/**
 * Initialize the location-edit admin page.
 */
export function initializeLocationEditPage () {
  const page = document.getElementById('admin-location-edit-page');

  if (!(page instanceof HTMLElement)) {
    return;
  }

  const leafletConfig = parseJsonDataset(requireDatasetValue(page, 'wtLeafletConfig', 'leaflet config'), 'leaflet config');
  const markerPosition = parseJsonDataset(requireDatasetValue(page, 'wtMarkerPosition', 'marker position'), 'marker position');
  const mapBounds = parseJsonDataset(requireDatasetValue(page, 'wtMapBounds', 'map bounds'), 'map bounds');
  const searchLabel = requireDatasetValue(page, 'wtSearchLabel', 'search label');
  const nothingFound = requireDatasetValue(page, 'wtNothingFound', 'nothing found message');
  const locationName = requireDatasetValue(page, 'wtLocationName', 'location name');
  const iconImagePath = requireDatasetValue(page, 'wtIconImagePath', 'leaflet icon image path');
  const addPlace = requireDatasetValue(page, 'wtAddPlace', 'add place mode') === '1';

  const form = requireElement(page, 'form', HTMLFormElement, 'location-edit form in #admin-location-edit-page');
  const latitudeField = requireElement(page, '#new_place_lati', HTMLInputElement, 'latitude field in #admin-location-edit-page');
  const longitudeField = requireElement(page, '#new_place_long', HTMLInputElement, 'longitude field in #admin-location-edit-page');
  const placeNameField = requireElement(page, '#new_place_name', HTMLInputElement, 'place name field in #admin-location-edit-page');

  // postcss_image_inliner breaks Leaflet autodetection of image paths.
  L.Icon.Default.imagePath = iconImagePath;

  const marker = L.marker(markerPosition, {
    draggable: true,
  });

  const resetCallback = (event) => {
    event.preventDefault();
    map.fitBounds(mapBounds, { padding: [50, 30] });
    marker.setLatLng(markerPosition);
    form.reset();
  };

  const geocoder = new L.Control.geocoder({
    position: 'bottomleft',
    defaultMarkGeocode: false,
    expand: 'click',
    showResultIcons: true,
    query: locationName,
    placeholder: placeNameField.labels?.[0]?.textContent?.trim() ?? 'Place',
    errorMessage: nothingFound,
    iconLabel: searchLabel,
  });

  const map = buildLeafletJsMap('wt-map', leafletConfig, resetCallback)
    .addControl(geocoder)
    .addLayer(marker)
    .fitBounds(mapBounds, { padding: [50, 30] });

  marker.on('dragend', () => {
    const coords = marker.getLatLng();
    map.panTo(coords);
    latitudeField.value = Number(coords.lat).toFixed(5);
    longitudeField.value = Number(coords.lng).toFixed(5);
  });

  geocoder.on('markgeocode', (result) => {
    const coords = result.geocode.center;
    const place = result.geocode.name.split(',', 1).toString();

    marker.setLatLng(coords);
    map.panTo(coords);

    if (addPlace) {
      placeNameField.value = place;
    }

    latitudeField.value = Number(coords.lat).toFixed(5);
    longitudeField.value = Number(coords.lng).toFixed(5);
  });

  map.on('zoomend', () => {
    if (!map.getBounds().contains(marker.getLatLng())) {
      map.panTo(marker.getLatLng());
    }
  });

  page.querySelectorAll('.editable').forEach((element) => {
    element.addEventListener('change', () => {
      const lat = latitudeField.value;
      const lng = longitudeField.value;

      marker.setLatLng([lat, lng]);
      map.panTo([lat, lng]);
    });
  });

  const geocoderIcon = page.querySelector('.leaflet-control-geocoder-icon');

  if (geocoderIcon instanceof HTMLElement) {
    geocoderIcon.setAttribute('title', searchLabel);
  }
}

