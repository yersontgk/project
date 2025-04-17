# Sistema de Gestión de Comedor Escolar

Sistema web para la gestión integral del comedor escolar, desarrollado con PHP y MySQL.

## Características

- Gestión de usuarios con diferentes roles (administrador, usuario base, cocinero)
- Control de asistencia por grado y sección
- Planificación de menús
- Cálculo automático de gramaje
- Gestión de inventario
- Generación de reportes
- Interfaz moderna y responsiva

## Requisitos

- PHP 7.4 o superior
- MySQL 5.7 o superior
- XAMPP (recomendado para desarrollo)
- Navegador web moderno

## Instalación

1. Clonar el repositorio en la carpeta `htdocs` de XAMPP:
```bash
git clone [url-repositorio] comedor-escolar
```

2. Importar la base de datos:
   - Abrir phpMyAdmin
   - Crear una nueva base de datos llamada `comedor_escolar`
   - Importar el archivo `config/schema.sql`

3. Configurar la conexión a la base de datos:
   - Copiar `config/database.example.php` a `config/database.php`
   - Modificar las credenciales según tu configuración

4. Acceder al sistema:
   - URL: `http://localhost/comedor-escolar`
   - Usuario: `yerson`
   - Contraseña: `123456789y`

## Estructura del Proyecto

```
comedor-escolar/
├── assets/
│   ├── css/
│   ├── js/
│   └── img/
├── config/
│   ├── database.php
│   └── schema.sql
├── controllers/
├── models/
├── views/
└── README.md
```

## Seguridad

- Autenticación de usuarios
- Contraseñas hasheadas
- Validación de formularios
- Protección contra SQL Injection
- Control de acceso basado en roles

## Contribución

1. Fork el repositorio
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo `LICENSE` para más detalles.