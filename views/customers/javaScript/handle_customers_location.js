document.addEventListener("DOMContentLoaded", () => {
    const map = L.map('map').setView([0, 0], 13);
    let marker;
  
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: 'Map data © OpenStreetMap contributors'
    }).addTo(map);
  
    function setLocation(lat, lng) {
      document.getElementById("latitude").value = lat;
      document.getElementById("longitude").value = lng;
  
      if (marker) {
        marker.setLatLng([lat, lng]);
      } else {
        marker = L.marker([lat, lng], { draggable: true }).addTo(map);
        marker.on('dragend', function(e) {
          const pos = e.target.getLatLng();
          setLocation(pos.lat, pos.lng);
          reverseGeocode(pos.lat, pos.lng);
        });
      }
  
      map.setView([lat, lng], 15);
      reverseGeocode(lat, lng);
    }
  
    function reverseGeocode(lat, lng) {
      const apiKey = "ea9cdcc4d06d452296b0b9429ae1f638";
      fetch(`https://api.opencagedata.com/geocode/v1/json?q=${lat}+${lng}&key=${apiKey}`)
        .then(res => res.json())
        .then(data => {
          if (data.results.length > 0) {
            document.getElementById("address").value = data.results[0].formatted;
          }
        });
    }
  
    // Get user location
    if ("geolocation" in navigator) {
      navigator.geolocation.getCurrentPosition(
        position => setLocation(position.coords.latitude, position.coords.longitude),
        error => {
          alert("Enable location to auto-fill your address.");
          map.setView([9.145, 40.4897], 6); // Ethiopia center fallback
        }
      );
    }
  });