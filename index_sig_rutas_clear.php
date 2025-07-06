<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VISOR EAP SIRENASO-SC</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Turf.js/6.5.0/turf.min.js"></script>
    <script src="https://unpkg.com/@turf/turf"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            display: flex;
            height: 100vh;
            font-family: Arial, sans-serif;
        }

        /* Barra lateral izquierda */
        #sidebar {
            width: 300px;
            background-color: #40E0D0; /* Color adjuntado */
            color: white;
            padding: 15px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            overflow-y: auto;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.3);
        }

        /* Imagen del logo */
        #logo {
            width: 290px; /* Ajusta el tamaño aquí */
            height: auto;
            margin-bottom: 20px;
        }


        /* Botones en la barra lateral */
        .button {
            background-color: white;
            color: black;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            transition: background-color 0.3s ease;
        }

        /* Lista de rutas */
        #route-list {
            width: 100%;
            background-color: #ffffff;
            color: black;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            text-align: left;
            max-height: 200px;
            overflow-y: auto;
        }

        .button:hover {
            background-color: #f0f0f0;
        }

        /* Contenedor del mapa */
        #map {
            flex: 1;
        }

        /* Formulario de nombres en la barra lateral */
        #route-names-form {
            display: none;
        }

        #route-names-form h4 {
            margin-bottom: 10px;
        }

        #route-names-form input {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: none;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        #route-names-form button {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <!-- Barra lateral -->
    <div id="sidebar">
        <img id="logo" src="logo_sf.png" alt="Logo Sirenaso SC">
        <button id="route-btn" class="button">Generar Ruta</button>
        <button id="clear-route-btn" class="button">Borrar Ruta</button>
        <button id="casos-por-comuna-btn" class="button">
            Casos por Comuna <span id="casos-count">(0)</span>
        </button>                
        <select id="comunas-select" class="button">
            <option value="">Seleccione una comuna</option>
        </select>        
        <button id="clear-route-btn" class="button">Seleccionar Caso Atendido</button>
        <select id="route-select" class="button" style="top: 60px; left: 10px;">
            <option value="">Seleccione una ruta</option>
        </select>
        <button id="delete-selected-route-btn" class="button">Borrar Ruta Seleccionada</button>

        <!-- Lista de rutas -->
        <div id="route-list">
            <h4>Rutas Guardadas</h4>
            <ul id="saved-routes"></ul>
        </div>

        <!-- Formulario para asignar nombres a las rutas -->
        <div id="route-names-form">
            <h4>Asignar Nombres a las Rutas</h4>
            <form id="names-form">
                <!-- Los inputs de nombres se generarán dinámicamente aquí -->
            </form>
        </div>
        <button id="save-route-btn" class="button">Guardar Ruta</button>
        <button id="logout-btn" class="button" onclick="window.location.href='cerrar_sesion.php';">  Cerrar Sesión  </button>
        <button id="manual-btn" class="button">Manual de Usuario</button>


    </div>

    <!-- Contenedor del mapa -->
    <div id="map"></div>

    <script>
        // Inicializar el mapa
        const map = L.map('map').setView([3.45, -76.53], 13); // Centrado en Cali
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
        }).addTo(map);

        //Capa wms desde geoserver:
        var CapaComunasOriente = L.tileLayer.wms("http://35.153.150.180:8080/geoserver/sirenaso_sc/wms", {
            layers: 'sirenaso_sc:comunas_o',
            format: 'image/png',
            transparent: true,
            attribution: 'Geoserver'
        }).addTo(map);
        
        //Capa wms desde geoserver:
        var CapaSeguridadCiudadana = L.tileLayer.wms("http://35.153.150.180:8080/geoserver/sirenaso_sc/wms", {
            layers: 'sirenaso_sc:seguridad_ciudadana',
            format: 'image/png',
            transparent: true,
            attribution: 'Geoserver'
        }).addTo(map);

        //Capa wms desde geoserver:
        var CapaIcbf = L.tileLayer.wms("http://35.153.150.180:8080/geoserver/sirenaso_sc/wms", {
            layers: 'sirenaso_sc:icbf',
            format: 'image/png',
            transparent: true,
            attribution: 'Geoserver'
        }).addTo(map);

        //Capa wms desde geoserver:
        var CapaEquipSalud = L.tileLayer.wms("http://35.153.150.180:8080/geoserver/sirenaso_sc/wms", {
            layers: 'sirenaso_sc:equip_salud',
            format: 'image/png',
            transparent: true,
            attribution: 'Geoserver'
        }).addTo(map);

        //Capa wms desde geoserver:
        var CapaEquipEdu = L.tileLayer.wms("http://35.153.150.180:8080/geoserver/sirenaso_sc/wms", {
            layers: 'sirenaso_sc:equip_educacion',
            format: 'image/png',
            transparent: true,
            attribution: 'Geoserver'
        }).addTo(map);

        //Capa wms desde geoserver:
        var CapaAmbulancias = L.tileLayer.wms("http://35.153.150.180:8080/geoserver/sirenaso_sc/wms", {
            layers: 'sirenaso_sc:ambulancias',
            format: 'image/png',
            transparent: true,
            attribution: 'Geoserver'
        }).addTo(map);



        let comunasData = []; // Variable global para almacenar los datos de las comunas

async function cargarCapaComunasWFS() {
    try {
        const urlWFS = "http://35.153.150.180:8080/geoserver/sirenaso_sc/ows?service=WFS&version=1.0.0&request=GetFeature&typeName=sirenaso_sc:comunas_o&outputFormat=application/json";

        const response = await fetch(urlWFS);
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }

        const data = await response.json();
        comunasData = data.features; // Guardar las comunas en la variable global

        const comunasLayer = L.geoJSON(data, {
            style: function (feature) {
                return { color: "#3388ff", weight: 2, opacity: 1 };
            },
            onEachFeature: function (feature, layer) {
                // Mostrar todas las propiedades de la comuna al hacer clic
                layer.on('click', function () {
                    console.log("Propiedades de la comuna:", feature.properties);
                    alert(`Comuna: ${feature.properties.nombre || "Sin nombre"}\nPropiedades: Revisa la consola.`);
                });
            }
        });

        comunasLayer.addTo(map);

        map.fitBounds(comunasLayer.getBounds());

        // Llenar el selector de comunas
        const comunasSelect = document.getElementById('comunas-select');
        comunasData.forEach((comuna, index) => {
            const option = document.createElement('option');
            option.value = index; // Usar el índice para identificar la comuna
            option.textContent = comuna.properties.nombre || `Comuna ${index + 1}`; // Ajustar según las propiedades
            comunasSelect.appendChild(option);
        });
    } catch (error) {
        console.error('Error al cargar la capa comunas_o:', error);
    }
}
        

    
        
        // Insertar aquí la función cargarCapaComunasWFS
        cargarCapaComunasWFS(); // Carga la capa comunas_o vía WFS


        // Cargar capa comunas_o desde GeoServer usando WFS
        async function cargarCapaComunasWFS() {
            try {
                const urlWFS = "http://35.153.150.180:8080/geoserver/sirenaso_sc/ows?service=WFS&version=1.0.0&request=GetFeature&typeName=sirenaso_sc:comunas_o&outputFormat=application/json";

                // Fetch de los datos GeoJSON desde GeoServer
                const response = await fetch(urlWFS);
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }

                const data = await response.json();

                // Crear una capa GeoJSON interactiva
                const comunasLayer = L.geoJSON(data, {
                   style: function (feature) {
                     return { color: "#3388ff", weight: 2, opacity: 1 }; // Estilo para las comunas
                    },
                    onEachFeature: function (feature, layer) {
                      // Agregar interacción al hacer clic en cada comuna
                     layer.on('click', function () {
                      const properties = feature.properties;
                      alert(`Comuna: ${properties.nombre}\nPoblación: ${properties.poblacion}`); // Ajusta los campos según tu capa
                });
            }
        });

        // Agregar la capa al mapa
        comunasLayer.addTo(map);

        // Ajustar la vista del mapa a la extensión de la capa
        map.fitBounds(comunasLayer.getBounds());

    } catch (error) {
        console.error('Error al cargar la capa comunas_o:', error);
    }
}


        //Opciones de control de capas
        L.control.layers({
            "Mapa Base":L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
            })

        },{'comunas':CapaComunasOriente, "Seguridad Ciudadana":CapaSeguridadCiudadana, "ICBF":CapaIcbf, "Equipamientos de Salud":CapaEquipSalud, "Equipamientod de Educacion":CapaEquipEdu, "Ambulancias":CapaAmbulancias}).addTo(map);

        // Marcadores para origen y destino
        let startMarker, endMarker;
        let currentPolyline = null; // Variable global para almacenar la ruta dibujada actualmente
        let ultimaRutaSeleccionada = null; // Variable para guardar el ID de la última ruta seleccionada
        let generatedRoutes = []; // Variable para almacenar las rutas generadas
        const reportMarkers = []; // Array para guardar los marcadores de reportes

        // Instanciar Leaflet Routing Machine
        let control = L.Routing.control({
            waypoints: [],
            routeWhileDragging: true,
            showAlternatives: true,
            altLineOptions: {
                styles: [{ color: 'green', weight: 4, opacity: 0.7 }]
            },
            lineOptions: {
                styles: [{ color: 'blue', weight: 4 }]
            },
            createMarker: function(i, waypoint, n) {
                return L.marker(waypoint.latLng, { draggable: true });
            }
        }).addTo(map);

        // Botones de la interfaz
        const routeBtn = document.getElementById('route-btn');
        const clearRouteBtn = document.getElementById('clear-route-btn');
        const routeNamesForm = document.getElementById('route-names-form');
        const namesForm = document.getElementById('names-form');
        const savedRoutes = document.getElementById('saved-routes');

        // Botón para abrir el manual de usuario
        const manualBtn = document.getElementById('manual-btn');
        manualBtn.addEventListener('click', function () {
            window.open('MANUAL DE USUARIO SIRENASO-SC.pdf', '_blank');
        });




        async function cargarRutasGuardadas() {
            try {
                const response = await fetch('obtener_rutas.php'); // Ruta al archivo PHP
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
        
                const data = await response.json();
        
                if (data.status === 'success') {
                    const savedRoutes = document.getElementById('saved-routes');
                    savedRoutes.innerHTML = ''; // Limpia la lista
        
                    data.rutas.forEach(ruta => {
                        // Crear un elemento de la lista para cada ruta
                        const li = document.createElement('li');
                        li.textContent = `${ruta.nombre} - Distancia: ${ruta.distancia} km - Duración: ${ruta.duracion} min`;
                        li.style.cursor = 'pointer';
        
                        // Agregar evento de clic para mostrar la ruta en el mapa
                        li.addEventListener('click', () => mostrarRutaEnMapa(ruta.geometria));
                        savedRoutes.appendChild(li);
                    });
                } else {
                    throw new Error(data.message || 'Error desconocido al cargar las rutas.');
                }
            } catch (error) {
                console.error('Error al cargar rutas guardadas:', error);
                const savedRoutes = document.getElementById('saved-routes');
                savedRoutes.innerHTML = '<li>Error al cargar las rutas.</li>';
            }
        }
                
        
        function mostrarRutaEnMapa(geom) {
            // Convertir la geometría WKT (LINESTRING) a un array de coordenadas
            const coordinates = geom
                .replace('LINESTRING(', '')
                .replace(')', '')
                .split(',')
                .map(coord => {
                    const [lng, lat] = coord.trim().split(' ');
                    return [parseFloat(lat), parseFloat(lng)];
                });
        
            // Dibujar la línea en el mapa
            if (currentPolyline) {
                map.removeLayer(currentPolyline); // Elimina cualquier línea previa
            }
        
            currentPolyline = L.polyline(coordinates, {
                color: 'blue',
                weight: 5,
                opacity: 0.7,
            }).addTo(map);
        
            // Ajustar la vista del mapa para mostrar toda la ruta
            map.fitBounds(currentPolyline.getBounds());
        }        


    // Evento para mostrar una ruta al seleccionarla del selector
    document.getElementById('route-select').addEventListener('change', async function () {
        const routeId = this.value; // Captura el ID de la ruta seleccionada
        
        if (routeId) {
            mostrarRutaDesdeBD(routeId); // Llama a la función para mostrar la ruta
        } else {
        // Limpia el mapa si no se selecciona ninguna ruta
        if (currentPolyline) {
            map.removeLayer(currentPolyline);
            currentPolyline = null;
        }
    }
});


// Evento para rastrear la última ruta seleccionada
document.getElementById('route-select').addEventListener('change', function () {
    const rutaId = this.value; // ID de la ruta seleccionada
    ultimaRutaSeleccionada = rutaId; // Actualiza la última ruta seleccionada
});

// Evento del botón para eliminar visualmente la última ruta seleccionada
document.getElementById('delete-selected-route-btn').addEventListener('click', function () {
    if (!currentPolyline) {
        alert('No hay una ruta dibujada en el mapa para eliminar.');
        return;
    }

    // Eliminar la ruta del mapa
    map.removeLayer(currentPolyline);
    currentPolyline = null;

    alert('La ruta ha sido eliminada del mapa.');
});


// Evento para mostrar una ruta nuevamente si se selecciona
document.getElementById('route-select').addEventListener('change', function () {
    const rutaId = this.value;
    const savedRoutes = document.getElementById('saved-routes');
    const routeItems = savedRoutes.querySelectorAll('li');

    if (!rutaId) return;

    routeItems.forEach((item) => {
        if (item.textContent.includes(this.options[this.selectedIndex].text)) {
            item.style.display = 'list-item';
        }
    });
});

document.getElementById('comunas-select').addEventListener('change', function () {
    const selectedIndex = this.value; // Índice de la comuna seleccionada
    if (selectedIndex === "") return; // Si no se selecciona ninguna comuna, salir

    const comuna = comunasData[selectedIndex]; // Obtener los datos de la comuna seleccionada
    const comunaGeometry = comuna.geometry;

    // Contar los marcadores dentro de la comuna
    let count = 0;
    reportMarkers.forEach(marker => {
        const markerLatLng = marker.getLatLng(); // Obtener latitud y longitud del marcador
        const markerPoint = turf.point([markerLatLng.lng, markerLatLng.lat]); // Crear punto GeoJSON del marcador

        const comunaPolygon = turf.polygon(comunaGeometry.coordinates); // Crear polígono GeoJSON de la comuna
        if (turf.booleanPointInPolygon(markerPoint, comunaPolygon)) { // Verificar si el marcador está dentro del polígono
            count++;
        }
    });

    // Mostrar la cantidad de casos en el cuadro "Casos por comuna"
    document.getElementById('casos-count').textContent = `(${count})`;

    // Mostrar nombre de la comuna seleccionada
    alert(`Comuna: ${comuna.properties.nombre || "Sin nombre"}\nMarcadores: ${count}`);
});


    
async function mostrarRutaDesdeBD(rutaId) {
    try {
        console.log(`ID de la ruta enviado: ${rutaId}`); // Para verificar el ID
        const response = await fetch(`obtener_geometria.php?id=${rutaId}`); // Enviar el ID al servidor
        const data = await response.json();

        if (data.status === 'success' && data.geometria) {
            // Eliminar cualquier ruta dibujada previamente
            if (currentPolyline) {
                map.removeLayer(currentPolyline);
                currentPolyline = null;
            }

            // Convertir la geometría LINESTRING en coordenadas
            const coordinates = data.geometria
                .replace('LINESTRING(', '')
                .replace(')', '')
                .split(',')
                .map(coord => {
                    const [lng, lat] = coord.trim().split(' ');
                    return L.latLng(parseFloat(lat), parseFloat(lng));
                });

            // Dibujar la geometría de la ruta como una línea en el mapa
            currentPolyline = L.polyline(coordinates, {
                color: 'blue',
                weight: 5,
                opacity: 0.7,
            }).addTo(map);

            // Ajustar la vista del mapa para mostrar la ruta completa
            map.fitBounds(currentPolyline.getBounds());
        } else {
            alert('No se pudo obtener la geometría de la ruta.');
        }
    } catch (error) {
        console.error('Error al mostrar la ruta desde la base de datos:', error);
        alert('Error al mostrar la ruta.');
    }
}
        
    // Función para visualizar una ruta en el mapa
    function visualizarRutaEnMapa(ruta) {
        // Limpiar rutas actuales del mapa
        control.setWaypoints([]);

        // Convertir la geometría LINESTRING en coordenadas
        const coordinates = ruta.geometria
            .replace('LINESTRING(', '')
            .replace(')', '')
            .split(',')
            .map(coord => {
                const [lng, lat] = coord.trim().split(' ');
                return L.latLng(parseFloat(lat), parseFloat(lng));
            });

        // Dibujar solo la geometría de la ruta como una línea en el mapa
        const polyline = L.polyline(coordinates, {
            color: 'blue',
            weight: 5,
            opacity: 0.7,
        }).addTo(map);

        // Ajustar la vista del mapa para mostrar la ruta completa
        map.fitBounds(polyline.getBounds());

        // Ocultar la tabla de indicaciones generadas automáticamente
        const routingContainer = document.querySelector('.leaflet-routing-container');
        if (routingContainer) {
            routingContainer.style.display = 'none'; // Ocultar indicaciones
        }

        // Confirmación al usuario
        alert(`Mostrando la ruta: ${ruta.nombre}`);
    }

    // Llamar a la función para cargar rutas al iniciar
    cargarRutasGuardadas();
        

   // Cargar los reportes en el mapa
        async function cargarReportes() {
            try {
                const response = await fetch('obtener_puntos.php');
                const datos = await response.json();

                datos.forEach(punto => {
                    const marker = L.marker([punto.latitud, punto.longitud]).addTo(map);
                    marker.bindPopup(`
                        <b>Nombre:</b> ${punto.nombre}<br>
                        <b>Fecha:</b> ${punto.fecha}<br>
                        <b>Nivel:</b> ${punto.nivel}<br>
                        <b>Descripción:</b> ${punto.descripcion}<br>
                        <img src="${punto.foto}" alt="Foto" style="width: 100px; height: auto;">
                    `);
                    reportMarkers.push(marker); // Guardar el marcador en el array
                });
            } catch (error) {
                console.error('Error al cargar los reportes:', error);
            }
        }

        cargarReportes(); // Llamar a la función para cargar reportes al iniciar
        cargarRutasGuardadas(); // Llamar a la función para cargar las rutas al iniciar
        cargarCapaComunasWFS(); // Llama a la función para cargar la capa WFS


        // Icono personalizado para marcador seleccionado
    const selectedIcon = L.icon({
    iconUrl: 'ruta_icono_seleccionado.png', // Cambia esto por la ruta de tu ícono
    iconSize: [25, 41], // Tamaño del ícono
    iconAnchor: [12, 41], // Punto de anclaje del ícono
    popupAnchor: [1, -34], // Punto de anclaje del popup
});

// Botón "Seleccionar Caso Atendido"
const selectCaseBtn = document.getElementById('clear-route-btn');
let selectingCase = false;

// Evento para alternar la funcionalidad del botón
selectCaseBtn.addEventListener('click', () => {
    selectingCase = !selectingCase; // Alternar entre activar y desactivar
    alert(
        selectingCase
            ? 'Modo de selección activado. Haz clic en un marcador para seleccionarlo.'
            : 'Modo de selección desactivado.'
    );
});

// Función para sincronizar con el servidor
async function actualizarEstadoMarcador(markerId) {
    try {
        const response = await fetch('actualizar_marcador.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: markerId, estado: 'seleccionado' }), // Cambiar según los datos necesarios
        });

        const data = await response.json();
        if (data.status === 'success') {
            console.log('Estado del marcador actualizado exitosamente:', data);
        } else {
            console.error('Error en la actualización:', data.message);
            alert('Hubo un problema al sincronizar el marcador con el servidor.');
        }
    } catch (error) {
        console.error('Error al enviar datos al servidor:', error);
        alert('Error al sincronizar el marcador con el servidor.');
    }
}

// Agregar evento click a los marcadores
reportMarkers.forEach((marker, index) => {
    marker.on('click', function () {
        if (selectingCase) {
            marker.setIcon(selectedIcon); // Cambia el ícono
            marker.openPopup(); // Abre el popup
            selectingCase = false; // Desactiva la selección
            alert('Marcador seleccionado.');

            // Enviar actualización al servidor
            actualizarEstadoMarcador(index); // Usa un ID único o los datos necesarios
        }
    });
});
                   
        
        // Manejador para el clic en el mapa
        map.on('click', function(e) {
            const { lat, lng } = e.latlng;
            if (!startMarker) {
                startMarker = L.marker([lat, lng], { draggable: true }).addTo(map).bindPopup('Inicio').openPopup();
            } else if (!endMarker) {
                endMarker = L.marker([lat, lng], { draggable: true }).addTo(map).bindPopup('Destino').openPopup();
            }
        });

        // Evento para generar la ruta
        routeBtn.addEventListener('click', function() {
            if (startMarker && endMarker) {
                const startLatLng = startMarker.getLatLng();
                const endLatLng = endMarker.getLatLng();
                control.setWaypoints([startLatLng, endLatLng]);
            } else {
                alert('Seleccione un punto de inicio y un destino en el mapa.');
            }
        });

        // Capturar las rutas generadas
        control.on('routesfound', function(e) {
            generatedRoutes = e.routes.map((ruta, index) => {
                const coordinates = ruta.coordinates.map(coord => `${coord.lng} ${coord.lat}`).join(', ');
                return {
                    nombre: `Ruta ${index + 1}`,
                    tipo_ruta: index === 0 ? 'principal' : 'alternativa',
                    distancia: (ruta.summary.totalDistance / 1000).toFixed(2),
                    duracion: (ruta.summary.totalTime / 60).toFixed(2),
                    geometria: `LINESTRING(${coordinates})`
                };
            });

            console.log('Rutas generadas:', generatedRoutes);
            showNamesForm();
        });

        // Mostrar el formulario de nombres
function showNamesForm() {
    namesForm.innerHTML = ''; // Limpiar formulario anterior
    generatedRoutes.forEach((ruta, index) => {
        const input = document.createElement('input');
        input.type = 'text';
        input.name = `nombre_ruta_${index}`;
        input.value = ruta.nombre;
        input.placeholder = `Nombre para Ruta ${index + 1}`;
        input.dataset.index = index; // Asociar índice al input
        namesForm.appendChild(input);
    });
    routeNamesForm.style.display = 'block';
}

// Botón para guardar ruta
const saveRouteBtn = document.getElementById('save-route-btn');

saveRouteBtn.addEventListener('click', async function () {
    if (generatedRoutes.length === 0) {
        alert('No hay rutas generadas para guardar.');
        return;
    }

    // Actualizar los nombres de las rutas con los valores del formulario
    const inputs = namesForm.querySelectorAll('input');
    inputs.forEach(input => {
        const index = input.dataset.index; // Obtener el índice del input
        generatedRoutes[index].nombre = input.value || generatedRoutes[index].nombre; // Actualizar nombre
    });

    try {
        const response = await fetch('guardar_ruta.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ rutas: generatedRoutes }),
        });

        const data = await response.json();
        if (data.status === 'success') {
            alert('Ruta(s) guardada(s) exitosamente.');
            cargarRutasGuardadas(); // Actualiza la lista de rutas guardadas
        } else {
            throw new Error(data.message || 'Error desconocido al guardar las rutas.');
        }
    } catch (error) {
        console.error('Error al guardar rutas:', error);
        alert('Hubo un problema al guardar las rutas.');
    }
});


        

        // Evento para borrar la ruta pero no los reportes
        clearRouteBtn.addEventListener('click', function() {
            control.setWaypoints([]);
            if (startMarker) map.removeLayer(startMarker);
            if (endMarker) map.removeLayer(endMarker);
            startMarker = null;
            endMarker = null;
            generatedRoutes = [];
            routeNamesForm.style.display = 'none'; // Ocultar formulario
        });

        // Evento para resaltar los casos por comuna
        document.getElementById('casos-por-comuna-btn').addEventListener('click', function () {
            // Obtener los marcadores y la capa de comunas
            const markers = reportMarkers; // Array global de marcadores
            const wmsLayer = CapaComunasOriente; // Capa WMS de las comunas
            if (markers.length === 0) {
                alert('No hay marcadores cargados en el mapa.');
                return;
            }
           
            document.getElementById('comunas-select').addEventListener('change', function () {
                const selectedIndex = this.value; // Índice de la comuna seleccionada
                if (selectedIndex === "") return; // Si no se selecciona ninguna comuna, salir
            
                const comuna = comunasData[selectedIndex]; // Obtener los datos de la comuna seleccionada
                const comunaGeometry = comuna.geometry;
            
                // Contar los marcadores dentro de la comuna
                let count = 0;
                reportMarkers.forEach(marker => {
                    const markerLatLng = marker.getLatLng(); // Obtener latitud y longitud del marcador
                    const markerPoint = turf.point([markerLatLng.lng, markerLatLng.lat]); // Crear punto GeoJSON del marcador
            
                    const comunaPolygon = turf.polygon(comunaGeometry.coordinates); // Crear polígono GeoJSON de la comuna
                    if (turf.booleanPointInPolygon(markerPoint, comunaPolygon)) { // Verificar si el marcador está dentro del polígono
                        count++;
                    }
                });
            
                // Mostrar la cantidad de casos en el cuadro "Casos por comuna"
                document.getElementById('casos-count').textContent = `(${count})`;
            });            
                


    // Iterar sobre los marcadores y verificar si están dentro de algún polígono WMS
    markers.forEach(marker => {
        const markerLatLng = marker.getLatLng();

        // Consultar el WMS para verificar si el marcador está dentro de algún polígono
        const url = wmsLayer._url + 
            `?SERVICE=WMS&VERSION=1.1.1&REQUEST=GetFeatureInfo&LAYERS=${wmsLayer.wmsParams.layers}&QUERY_LAYERS=${wmsLayer.wmsParams.layers}` + 
            `&BBOX=${map.getBounds().toBBoxString()}&FEATURE_COUNT=1&HEIGHT=${map.getSize().y}&WIDTH=${map.getSize().x}` + 
            `&INFO_FORMAT=application/json&SRS=EPSG:4326&X=${map.latLngToContainerPoint(markerLatLng).x}&Y=${map.latLngToContainerPoint(markerLatLng).y}`;

        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.features && data.features.length > 0) {
                    // Si el marcador está dentro de una comuna, resáltalo
                    marker.setIcon(
                        L.icon({
                            iconUrl: 'icono_resaltado.png', // Icono personalizado
                            iconSize: [25, 41],
                            iconAnchor: [12, 41],
                            popupAnchor: [1, -34],
                        })
                    );
                    marker.openPopup(); // Abrir el popup automáticamente si está dentro
                }
            })
            .catch(error => {
                console.error('Error al consultar el WMS:', error);
            });
    });

    alert('Marcadores resaltados según las comunas.');
});

    </script>
    <script>

    </script>
              
    </body>
</HTml>
       

