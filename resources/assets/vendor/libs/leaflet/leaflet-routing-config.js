import leaflet from 'leaflet';
import 'leaflet-routing-machine';

// Optionally attach Leaflet Routing Machine to the global scope for easier usage
try {
  window.leafletRouting = leaflet.Routing;
} catch (e) {
  console.error('Failed to attach Leaflet Routing to window:', e);
}

export { leaflet };
