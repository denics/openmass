/**
 * @file
 * Renders map on .js-google-map div for map page.
 *
 * Loads google maps results and filters (loads once)
 *
 * Copyright 2017 Palantir.net, Inc.
 */
var gmarkers = [];

(function ($, Drupal) {
  'use strict';
  Drupal.behaviors.massMap = {
    attach: function (context, settings) {
      var mapId = '.js-google-map';
      var locations = drupalSettings.locations;
      var markers = [];

      // Using once() to apply the myCustomBehaviour effect when you want to do just run one function.
      $(context).find(mapId).once(mapId).addClass('mass-map-processed').each(function () {

        // Create a map with its center at the center of MA
        var mapProp = {
          center: new google.maps.LatLng(42.4072107, -71.3824374),
          zoom: 8,
          scrollwheel: false
        };
        var map = new google.maps.Map($(this)[0], mapProp);
        // Keep track of the bounds so we can adjust based on markers.
        var bounds = new google.maps.LatLngBounds();
        // Info windows to label map points
        var infowindow = new google.maps.InfoWindow();

        // Create the autocomplete object and associate it with the UI input control.
        // Restrict the search to the default country, and to place type "cities".
        var autocomplete = new google.maps.places.Autocomplete(document.getElementById('filter-by-location'));

        var geocoder = new google.maps.Geocoder();

        // Attach a click to this button.
        $('.ma__button-search').click(function (event) {
          event.preventDefault();
          onPlaceChanged();
        });

        // Reset location message.
        $('.js-location-listing-results > p').text('');

        // When the user selects a city, get the place details for the city and
        // zoom the map in on the city.
        function onPlaceChanged() {
          // A wrapper function so the geocoder has a chance to finish.
          var locationSort = function (place) {
            // Reset bounds to remove previous search locations.
            bounds = new google.maps.LatLngBounds();
            // Get the location points from the filter and do some nifty stuff.
            bounds.extend(place.geometry.location);

            // Lets get distance number on all our existing markers.
            for (var key in markers) {
              if (markers.hasOwnProperty(key)) {
                markers[key].distance = google.maps.geometry.spherical.computeDistanceBetween(place.geometry.location, markers[key].getPosition());
                // Extend the bounds to include each marker's position.
                bounds.extend(markers[key].position);
              }
            }

            map.fitBounds(bounds);

            // Lets sort our existing markers to get the closest locations.
            markers.sort(function (a, b) {
              return a.distance - b.distance;
            });

            // Sort global markers for external links.
            gmarkers.sort(function (a, b) {
              return a.distance - b.distance;
            });

            // Remove our previous results.
            $('.js-location-listing-results').html('');

            // Add our location message.
            $('.js-location-listing-results').append('<p>Showing ' + markers.length + ' locations nearby: ' + place.name + '</p>');

            // Lets render our new sorted location listing --
            // using the _nid property we set earlier.
            for (var index in markers) {
              if (markers.hasOwnProperty(index)) {
                $('.js-location-listing-results').append(listingRow(locations.imagePromos[markers[index]._nid], index));
              }
            }
          };
          if (autocomplete.getPlace()) {
            locationSort(autocomplete.getPlace());
          }
          else {
            // If we only have a string use geocoder to get a location
            var getAddr = function (addr, f) {
              // Wrap it in a function so it is not called asynchronously.
              geocoder.geocode({address: addr}, function (results, status) {
                if (status === google.maps.GeocoderStatus.OK) {
                  f(results[0]);
                }
              });
              return -1;
            };
            getAddr(document.getElementById('filter-by-location').value, function (res) {
              locationSort(res);
            });
          }
        }

        // Go over list of locations,.
        for (var key in locations.googleMap.markers) {
          if (locations.googleMap.markers.hasOwnProperty(key)) {
            var infoWindowData = infoWindow(locations.googleMap.markers[key].infoWindow);
            // Set the marker of the location and attach some custom data to it.
            var marker = new google.maps.Marker({
              position: new google.maps.LatLng(
                locations.googleMap.markers[key].position.lat,
                locations.googleMap.markers[key].position.lng),
              _windowInfo: infoWindowData,
              _nid: key
            });
            // Put markers on map.
            marker.setMap(map);
            // Set up our markers variable for later.
            markers.push(marker);
            // Push to global markers for external links.
            gmarkers.push(marker);
            // Extend the bounds to include each marker's position.
            bounds.extend(marker.position);
            // Add information to the info window of that marker.
            google.maps.event.addListener(marker, 'click', (function (marker, infoWindowData) {
              return function () {
                infowindow.setContent(infoWindowData);
                infowindow.open(map, marker);
              };
            })(marker, infoWindowData));
          }
        }
        // Now fit the map to the newly inclusive bounds.
        map.fitBounds(bounds);

        $('.js-location-listing-link').each(function (index) {
          $(this).on('click', function (event) {
            event.preventDefault();
            triggerClick(index);
          });
        });
      });
    }
  };
})(jQuery, Drupal);

var infoWindow = function (infoWindow) {
  'use strict';
  var info = '';
  // infoWindow data.
  if (infoWindow.name) {info += '<h3 class="ma__info-window__name">' + infoWindow.name + '</h3>';}
  if (infoWindow.address) {info += '<p class="ma__info-window__address">' + infoWindow.address + '</p>';}
  if (infoWindow.phone) {info += '<div class="ma__info-window__phone"><span class="ma__info-window__label">Phone:&nbsp;</span><a class="ma__info-window__phone" href="tel:' + infoWindow.phone + '">' + infoWindow.phone + '</a></div>';}
  if (infoWindow.email) {info += '<div class="ma__info-window__email"><span class="ma__info-window__label">Email:&nbsp;</span><a class="ma__info-window__email" href="mailto:' + infoWindow.email + '">' + infoWindow.email + '</a></div>';}
  info = '<section class="ma__info-window">' + info + '</section>';
  return info;
};

var listingRow = function (listing, index) {
  'use strict';
  var info = '';
  // listing data.
  if (listing.image.image) {info += '<a href="' + listing.title.href + '"><div class="ma__image-promo__image"><img alt="image" src="' + listing.image.image + '"></div></a>';}
  info += '<div class="ma__image-promo__details">';
  if (listing.title.text) {info += '<h2 class="ma__image-promo__title"><span class="ma__decorative-link"><a href="' + listing.title.href + '" class="js-clickable-link">' + listing.title.text + '&nbsp;<svg aria-hidden="true" id="SvgjsSvg1000" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs" width="16" height="18" viewBox="0 0 16 18"><defs id="SvgjsDefs1001"></defs><path id="SvgjsPath1007" d="M983.721 1887.28L983.721 1887.28L986.423 1890L986.423 1890L986.423 1890L983.721 1892.72L983.721 1892.72L978.318 1898.17L975.617 1895.45L979.115 1891.92L971.443 1891.92L971.443 1888.0700000000002L979.103 1888.0700000000002L975.617 1884.5500000000002L978.318 1881.8300000000002Z " transform="matrix(1,0,0,1,-971,-1881)"></path></svg></a></span></h2>';}
  if (listing.location.text) {info += '<div class="ma__image-promo__location"><svg aria-hidden="true" id="SvgjsSvg1000" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs" width="14" height="20" viewBox="0 0 14 20"><defs id="SvgjsDefs1001"></defs><path id="SvgjsPath1007" d="M1136 2272C1134.13 2272 1132.37 2272.73 1131.05 2274.05C1128.61 2276.5 1128.3 2281.11 1130.3999999999999 2283.9L1135.9999999999998 2292L1141.5999999999997 2283.91C1143.6999999999996 2281.1099999999997 1143.3899999999996 2276.5 1140.9499999999996 2274.0499999999997C1139.6299999999997 2272.7299999999996 1137.8699999999997 2271.9999999999995 1135.9999999999995 2271.9999999999995ZM1133.51 2278.94C1133.51 2277.53 1134.66 2276.38 1136.07 2276.38C1137.47 2276.38 1138.62 2277.53 1138.62 2278.94C1138.62 2280.35 1137.4699999999998 2281.5 1136.07 2281.5C1134.6599999999999 2281.5 1133.51 2280.35 1133.51 2278.94Z " fill-opacity="1" transform="matrix(1,0,0,1,-1129,-2272)"></path></svg><a class="js-location-listing-link" href="javascript:triggerClick(' + index + ')" role="button">' + listing.location.text + '</a></div>';}
  if (listing.description.rteElements[0].data.rawHtml.content) {info += '<div class="ma__image-promo__description"><section class="ma__rich-text js-ma-rich-text"><p>' + listing.description.rteElements[0].data.rawHtml.content + '</p></section></div>';}
  if (listing.location.text) {info += '<span class="ma__decorative-link"><a href="https://www.google.com/maps/place/' + encodeURI(listing.location.text) + '" class="js-clickable-link">Directions&nbsp;<svg aria-hidden="true" id="SvgjsSvg1000" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs" width="17" height="16" viewBox="0 0 17 16"><defs id="SvgjsDefs1001"></defs><path id="SvgjsPath1007" d="M739.199 92.7986L739.199 80L735.9989999999999 80L735.9989999999999 95.9983L751.9979999999999 95.9983L751.9979999999999 92.7986ZM743.294 81.0032L743.305 83.5605L746.6049999999999 83.5737L742.9939999999999 87.1849L744.809 89.0001L748.4259999999999 85.3836L748.4399999999999 88.6956L750.997 88.7058L750.976 83.59129999999999L750.966 81.03389999999999L748.409 81.02349999999998L748.409 81.02369999999999Z " transform="matrix(1,0,0,1,-735,-80)"></path></svg></a></span>';}
  info += '</div>';
  info = '<section class="ma__image-promo">' + info + '</section>';
  return info;
};

function triggerClick(i) {
  'use strict';
  google.maps.event.trigger(gmarkers[i], 'click');
}
