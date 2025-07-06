const express = require('express');
const bodyParser = require('body-parser');
const { Pool } = require('pg');
const path = require('path'); // Para servir archivos estáticos

// Configuración de conexión a la base de datos PostgreSQL
const pool = new Pool({
  user: 'postgre', // Cambia por tu usuario de PostgreSQL
  host: 'localhost', // Cambia si tu base está en otro host
  database: 'sirenaso_sc', // Nombre de tu base de datos
  password: 'Armendarisa123.', // Contraseña del usuario de PostgreSQL
  port: 5432, // Puerto de PostgreSQL (por defecto es 5432)
});

const app = express();
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: true }));

// Ruta para servir el visor como archivo estático
app.use(express.static(path.join(__dirname, 'public')));

// Ruta para inicio de sesión de "comunidad"
app.post('/login-comunidad', async (req, res) => {
  const { codigo, contraseña } = req.body;

  try {
    const result = await pool.query(
      'SELECT * FROM comunidad WHERE codigo = $1 AND contraseña = $2',
      [codigo, contraseña]
    );

    if (result.rows.length > 0) {
      // Redirigir al visor si el inicio de sesión es exitoso
      res.status(200).send({ redirect: '/index.html' });
    } else {
      res.status(401).send({ message: 'Código o contraseña incorrectos' });
    }
  } catch (error) {
    res.status(500).send({ message: 'Error del servidor', error });
  }
});

// Ruta para inicio de sesión de "EAP"
app.post('/login-eap', async (req, res) => {
  const { codigo, contraseña } = req.body;

  try {
    const result = await pool.query(
      'SELECT * FROM eap WHERE codigo = $1 AND contraseña = $2',
      [codigo, contraseña]
    );

    if (result.rows.length > 0) {
      // Redirigir al visor si el inicio de sesión es exitoso
      res.status(200).send({ redirect: '/index.html' });
    } else {
      res.status(401).send({ message: 'Código o contraseña incorrectos' });
    }
  } catch (error) {
    res.status(500).send({ message: 'Error del servidor', error });
  }
});

// Servidor en ejecución
app.listen(3000, () => {
  console.log('Servidor corriendo en http://localhost:8082');
});
