<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VISOR COMUNIDAD</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        /* Estilos para el modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        .geov {
            z-index: 0;
            height: 740px; 
            width: 100%;
        }
    </style>
</head>
<body>
    <div id="sidebar">
    <img id="logo" src="logo_sf.png" alt="Logo Sirenaso SC">
    <button id="manual-btn" class="button">Manual de Usuario</button>
    <button id="add-point-btn">Reportar</button>
    <button id="logout-btn" class="button" onclick="window.location.href='cerrar_sesion.php';">  Cerrar Sesión  </button>
    <div id="map" class="geov"></div>

    <!-- Modal para el formulario de registro -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Formulario de Registro</h2>
            <form id="form-registro" method="POST" action="guardar_datos.php">
                <label>Nombre:</label>
                <input type="text" name="nombre" id="nombre" required><br>
                <label>Fecha:</label>
                <input type="date" name="fecha" id="fecha" required><br>
                <label>Nivel de Situación:</label>
                <select name="nivel" id="nivel">
                    <option value="NIVEL I">NIVEL I</option>
                    <option value="NIVEL II">NIVEL II</option>
                    <option value="NIVEL III">NIVEL III</option>
                </select><br>
                <label>Descripción:</label>
                <textarea name="descripcion" id="descripcion" required></textarea><br>
                <label>Foto (URL):</label>
                <input type="text" name="foto" id="foto"><br>
                <label>Latitud:</label>
                <input type="text" name="latitud" id="latitud" readonly><br>
                <label>Longitud:</label>
                <input type="text" name="longitud" id="longitud" readonly><br>
                <button type="submit">Registrar</button>
            </form>
        </div>
    </div>

    <script>
        // Inicializar el mapa
        const map = L.map('map').setView([3.45, -76.53], 13); // Centrado en Cali
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
        }).addTo(map);

        let marker;
        let addingPoint = false;


        // Obtener el modal
        const modal = document.getElementById("myModal");

        // Obtener el botón de cerrar
        const span = document.getElementsByClassName("close")[0];

        // Obtener el botón de agregar punto
        const addPointBtn = document.getElementById("add-point-btn");

        // Evento para activar el modo de agregar puntos
        addPointBtn.onclick = function() {
            addingPoint = !addingPoint;
            addPointBtn.textContent = addingPoint ? "Cancelar" : "Agregar Punto";
        };

        // Evento para agregar un marcador y mostrar el modal
        map.on('click', function (e) {
            if (!addingPoint) return;

            const { lat, lng } = e.latlng;
            if (marker) {
                map.removeLayer(marker);
            }
            marker = L.marker([lat, lng]).addTo(map);

            // Llenar el formulario con las coordenadas
            document.getElementById('latitud').value = lat;
            document.getElementById('longitud').value = lng;

            // Mostrar el modal
            modal.style.display = "block";
        });

        // Cuando el usuario hace clic en la "x", cerrar el modal
        span.onclick = function() {
            modal.style.display = "none";
        }

        // Cuando el usuario hace clic fuera del modal, cerrarlo
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        // Abrir el PDF del manual de usuario en una nueva pestaña
         document.getElementById("manual-btn").addEventListener("click", () => {
           window.open('MANUAL DE USUARIO SIRENASO-SC.pdf', '_blank');
        });


        // Función para cargar los puntos desde el servidor
        async function cargarPuntos() {
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
                });
            } catch (error) {
                console.error('Error al cargar los puntos:', error);
            }
        }

        // Cargar los puntos al iniciar la página
        cargarPuntos();
    </script>
    <script>
        const map = L.map('map').setView([3.45, -76.53], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
        }).addTo(map);
    
        let marker;
    
        // Cargar puntos desde el servidor
        async function cargarPuntos() {
            const response = await fetch('puntos.php');
            const puntos = await response.json();
    
            puntos.forEach(punto => {
                L.marker([punto.latitud, punto.longitud]).addTo(map).bindPopup(`
                    <b>Nombre:</b> ${punto.nombre}<br>
                    <b>Fecha:</b> ${punto.fecha}<br>
                    <b>Nivel:</b> ${punto.nivel}<br>
                    <b>Descripción:</b> ${punto.descripcion}<br>
                    <img src="${punto.foto}" alt="Foto" style="width: 100px; height: auto;">
                `);
            });
        }
    
        // Guardar un nuevo punto
        async function guardarPunto(lat, lng) {
            const nuevoPunto = {
                nombre: 'Nuevo Punto',
                fecha: new Date().toISOString().split('T')[0],
                nivel: 'NIVEL I',
                descripcion: 'Descripción del nuevo punto',
                foto: '',
                latitud: lat,
                longitud: lng
            };
    
            await fetch('puntos.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(nuevoPunto)
            });
    
            cargarPuntos(); // Recargar puntos
        }
    
        // Evento para agregar un marcador
        map.on('click', function (e) {
            const { lat, lng } = e.latlng;
            if (marker) {
                map.removeLayer(marker);
            }
            marker = L.marker([lat, lng]).addTo(map).bindPopup('Nuevo Punto').openPopup();
    
            guardarPunto(lat, lng);
        });
    
        // Cargar los puntos al iniciar
        cargarPuntos();
    </script>
    
</body>
</html>