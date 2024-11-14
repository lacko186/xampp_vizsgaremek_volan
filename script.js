const markerImage = '285659_marker_map_icon.png'; // Marker image URL
const streetViewImage = 'man.png'; // Street view marker URL

var map = L.map('mapContainer').setView([47.1625, 19.5033], 7);

// Initial map layer (default map type)
let currentLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors',
    maxZoom: 18
}).addTo(map);

function setMapType(type) {
    map.removeLayer(currentLayer); // Remove previous layer
    switch (type) {
        case 'roadmap':
            currentLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors',
                maxZoom: 18
            });
            break;
        case 'satellite':
            currentLayer = L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenTopoMap contributors',
                maxZoom: 17
            });
            break;
        case 'terrain':
            currentLayer = L.tileLayer('https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors',
                maxZoom: 18
            });
            break;
        case 'hybrid':
            currentLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors',
                maxZoom: 18
            });
            break;
    }
    currentLayer.addTo(map); // Add new layer
}

function getLatLngByCity(city) {
    return new Promise((resolve, reject) => {
        fetch(`https://nominatim.openstreetmap.org/search?city=${city}&format=json`)
            .then(response => response.json())
            .then(data => {
                if (data && data.length > 0) {
                    resolve([data[0].lat, data[0].lon]);
                } else {
                    reject(`No coordinates found for ${city}`);
                }
            })
            .catch(error => reject(error));
    });
}

async function connectCities() {
    const fromCity = document.getElementById('FromCity').value;
    const toCity = document.getElementById('ToCity').value;

    try {
        const fromCoords = await getLatLngByCity(fromCity);
        const toCoords = await getLatLngByCity(toCity);

        if (fromCoords && toCoords) {
            const fromLatLng = L.latLng(fromCoords[0], fromCoords[1]);
            const toLatLng = L.latLng(toCoords[0], toCoords[1]);

            L.marker(fromLatLng, {
                icon: L.icon({
                    iconUrl: markerImage,
                    iconSize: [25, 41]
                })
            }).addTo(map).bindPopup(`Honnan: ${fromCity}`).openPopup();

            L.marker(toLatLng, {
                icon: L.icon({
                    iconUrl: markerImage,
                    iconSize: [25, 41]
                })
            }).addTo(map).bindPopup(`Hova: ${toCity}`).openPopup();

            const apiKey = '5b3ce3597851110001cf6248cab08206a8ee4ef7b2a341cfc89a63bd';
            const response = await fetch(`https://api.openrouteservice.org/v2/directions/driving-car?api_key=${apiKey}&start=${fromCoords[1]},${fromCoords[0]}&end=${toCoords[1]},${toCoords[0]}`);
            const data = await response.json();

            if (data.features && data.features.length > 0) {
                const routeCoordinates = data.features[0].geometry.coordinates;
                const routeLatLngs = routeCoordinates.map(coord => [coord[1], coord[0]]);
                const polyline = L.polyline(routeLatLngs, { color: 'red' }).addTo(map);
                map.fitBounds(polyline.getBounds());
            } else {
                console.error("Nem sikerült útvonalat találni a megadott pontok között.");
            }
        } else {
            alert("Nem találhatók koordináták az egyik város számára.");
        }
    } catch (error) {
        alert(error);
    }
}

function updateClock() {
    const now = new Date();
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');
    document.getElementById('time').innerText = `${hours}:${minutes}:${seconds}`;
}

setInterval(updateClock, 1000);
updateClock();

// Draggable street view marker
let streetViewMarker = L.marker(map.getCenter(), {
    draggable: true,
    icon: L.icon({
        iconUrl: streetViewImage,
        iconSize: [50, 50]
    })
}).addTo(map);

streetViewMarker.on('dragend', function (e) {
    const latlng = streetViewMarker.getLatLng();
    let streetViewPopup = L.popup()
        .setLatLng(latlng)
        .setContent(`<iframe width="100%" height="350" frameborder="0" style="border:0" 
        src="https://www.google.com/maps/embed/v1/streetview?key=YOUR_GOOGLE_MAPS_API_KEY&location=${latlng.lat},${latlng.lng}" 
        allowfullscreen></iframe>`)
        .openOn(map);
});
