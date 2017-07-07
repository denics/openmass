(function () {
  'use strict';
  window.initGoogleMaps = function () {
    // Set a flag that the library has loaded, in case google maps js misses event.
    window.googleMapsLoaded = true;
    var mapsLibLoadEvent = new CustomEvent('ma:LibrariesLoaded:GoogleMaps');
    // Emit an event that the library has loaded.
    document.dispatchEvent(mapsLibLoadEvent);
  };

})();
