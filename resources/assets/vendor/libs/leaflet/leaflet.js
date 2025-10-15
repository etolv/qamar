import leaflet from 'leaflet';
import 'leaflet-routing-machine';

import markerIcon2x from 'leaflet/dist/images/marker-icon-2x.png';
import markerIcon from 'leaflet/dist/images/marker-icon.png';
import markerShadow from 'leaflet/dist/images/marker-shadow.png';

// Override default marker icons
delete leaflet.Icon.Default.prototype._getIconUrl;

leaflet.Icon.Default.mergeOptions({
  iconRetinaUrl: markerIcon2x,
  iconUrl: markerIcon,
  shadowUrl: markerShadow
});

// Optionally attach Leaflet and Routing Machine to the global `window` object
try {
  window.leaflet = leaflet;
  window.leafletRouting = leaflet.Routing;
} catch (e) {
  console.error('Failed to attach Leaflet or Routing to window:', e);
}

export { leaflet };
