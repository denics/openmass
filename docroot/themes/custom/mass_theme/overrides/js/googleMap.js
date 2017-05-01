(function ($, Drupal) {
  'use strict';
  Handlebars.getTemplate = function(name) {
    if (Handlebars.templates === undefined || Handlebars.templates[name] === undefined) {
      $.ajax({
        url : themePath + '/js/templates/' + name + '.html',
        success : function(data) {
          if (Handlebars.templates === undefined) {
            Handlebars.templates = {};
          }
          Handlebars.templates[name] = Handlebars.compile(data);
        },
        async : false
      });
    }
    return Handlebars.templates[name];
  };

  // only run this code if there is a google map component on the page
  if(!$('.js-google-map').length || typeof googleMapData === 'undefined'){
    return;
  }

  let compiledTemplate = Handlebars.getTemplate('googleMapInfo');

  // after the api is loaded this function is called
  window.initMap = function() {

    $(".js-google-map").each(function(i) {
      const $el = $(this);

      // get the maps data
      // this could be replaced with an api
      const rawData = googleMapData[i];

      // *** Create the Map *** //
      // map defaults
      const initMapData = {
        scrollwheel: false
      }
      // create map Data by combining the rawData with the defaults
      const mapData = Object.assign({}, rawData.map, initMapData);

      const map = new google.maps.Map(this, mapData);

      let markers = [];
      var bounds = new google.maps.LatLngBounds();

      // *** Add Markers with popups *** //
      rawData.markers.forEach(function(d,i){
        let markerData = Object.assign({map},d);

        let marker =  new google.maps.Marker(markerData);

        let infoData = infoTransform(markerData.infoWindow);
        let template = compiledTemplate(infoData);
        let infoWindow = new google.maps.InfoWindow({
          content: template
        });

        marker.addListener('click', function(){
          infoWindow.open(map, marker);
        });

        marker.showInfo = () => {
          infoWindow.open(map, marker);
        }

        markers.push(marker);

        //extend the bounds to include each marker's position
        bounds.extend(marker.position);
      });

      //now fit the map to the newly inclusive bounds
      map.fitBounds(bounds);

      // listen for recenter command
      $el.on( "recenter", function( event, markerIndex ) {
        if(typeof markers[markerIndex] === "undefined") {
          return false;
        }
        map.setCenter(markers[markerIndex].getPosition());
        map.fitBounds(bounds);
        markers[markerIndex].showInfo();
      });
    });
  }

  function infoTransform(data) {
    let infoData = {
      phoneFormatted: formatPhone(data.phone),
      faxFormatted: formatPhone(data.fax)
    }
    return Object.assign({},data,infoData);
  }

  function formatPhone(phone) {
    let phoneTemp = phone[0] === '1' ? phone.substring(1) : phone;
    return phoneTemp.replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3');
  }

  // load Google's api
  var script = document.createElement('script');
  script.src = "//maps.googleapis.com/maps/api/js?key=AIzaSyC-WIoNfS6fh7TOtOqpDEgKST-W_NBebTk&callback=initMap";
  document.getElementsByTagName('head')[0].appendChild(script);


})(jQuery, Drupal);
