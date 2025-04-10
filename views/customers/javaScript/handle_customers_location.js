let map;
let marker;

function initMap() {
  const defaultLocation = { lat: 9.145, lng: 40.4897 }; // Ethiopia center
  map = new google.maps.Map(document.getElementById("map"), {
    zoom: 15,
    center: defaultLocation,
  });

  // Try to get user's location
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
      position => {
        const userLocation = {
          lat: position.coords.latitude,
          lng: position.coords.longitude,
        };
        setLocation(userLocation);
      },
      () => {
        alert("Enable location to auto-fill your address.");
        map.setCenter(defaultLocation);
      }
    );
  } else {
    map.setCenter(defaultLocation);
  }

  // Add click listener to allow manual selection
  map.addListener("click", function (event) {
    setLocation(event.latLng);
  });
}

function setLocation(latlng) {
  document.getElementById("latitude").value = latlng.lat;
  document.getElementById("longitude").value = latlng.lng;

  if (!marker) {
    marker = new google.maps.Marker({
      position: latlng,
      map: map,
      draggable: true,
    });

    marker.addListener("dragend", function () {
      const newPos = marker.getPosition();
      document.getElementById("latitude").value = newPos.lat();
      document.getElementById("longitude").value = newPos.lng();
      reverseGeocode(newPos);
    });
  } else {
    marker.setPosition(latlng);
  }

  map.setCenter(latlng);
  reverseGeocode(latlng);
}

function reverseGeocode(latlng) {
  const geocoder = new google.maps.Geocoder();

  geocoder.geocode({ location: latlng }, (results, status) => {
    if (status === "OK" && results[0]) {
      document.getElementById("address").value = results[0].formatted_address;
    }
  });
}