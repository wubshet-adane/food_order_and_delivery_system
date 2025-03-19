let map;
let service;
let infowindow;

function initMap() {
    // Get values from input fields
    let lat = parseFloat(document.getElementById("latitude").value);
    let lng = parseFloat(document.getElementById("longtude").value);
    let hotel_location = document.getElementById("location").value;

    if (isNaN(lat) || isNaN(lng)) {
        console.error("Invalid latitude or longitude");
        return;
    }

    const location = new google.maps.LatLng(lat, lng);

    infowindow = new google.maps.InfoWindow();
    map = new google.maps.Map(document.getElementById("map"), {
        center: location,
        zoom: 50,
    });

    const request = {
        query: hotel_location,
        fields: ["name", "geometry"],
    };

    service = new google.maps.places.PlacesService(map);
    service.findPlaceFromQuery(request, (results, status) => {
        if (status === google.maps.places.PlacesServiceStatus.OK && results) {
            results.forEach(createMarker);
            map.setCenter(results[0].geometry.location);
        }
    });
}

function createMarker(place) {
    if (!place.geometry || !place.geometry.location) return;

    const marker = new google.maps.Marker({
        map,
        position: place.geometry.location,
    });

    google.maps.event.addListener(marker, "click", () => {
        infowindow.setContent(place.name || "");
        infowindow.open(map);
    });
}

window.initMap = initMap;
