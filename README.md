# flight-survey-management-app

A web-based tool for local use, designed to manage flights from the Canary Islands: track statuses, log surveys, import/export data, and visualize statistics.

---

## Requisitos

Asegúrate de tener instalado lo siguiente:

- PHP 7.4 o superior
- Composer
- Servidor web (Apache, XAMPP, etc.)
- MySQL o MariaDB

---

## Instalación

### 1. Clonar el repositorio

Para obtener una copia local del repositorio, abre una terminal (CMD, PowerShell o Terminal de VSCode) y ejecuta lo siguiente:

```bash
git clone https://github.com/deritf/flight-survey-management-app.git
```

### 2. Instalar dependencias con Composer

La carpeta `vendor` está excluida del repositorio mediante `.gitignore`.

Si estás usando Visual Studio Code, puedes abrir la terminal con: Ctrl + ñ (o desde el menú: Ver -> Terminal). Una vez en la terminal del proyecto, ejecuta:

```bash
composer install
```

Este comando descargará e instalará todas las dependencias definidas en el archivo `composer.json` y generará automáticamente la carpeta `vendor`.

### 3. Crear la base de datos

- Accede a phpMyAdmin o tu gestor de base de datos.
- Crea una base de datos con el nombre: `aplicacion_vuelo_sec`.
- Importa el archivo `aplicacion_vuelo_sec.sql` ubicado en la carpeta `base de datos estructura`, situada en el directorio raíz del proyecto.

### 4. Configurar la conexión a la base de datos

Edita el archivo:

```
config/conexion.php
```

Y modifica las credenciales para que coincidan con tu base de datos:

```php
$host = "localhost";
$usuario = "tu_usuario";
$password = "tu_contraseña";
$basededatos = "aplicacion_vuelo_sec";
```

### 5. Configurar los datos de envío de informes

Edita el archivo:

```
conf/.env
```

Y completa los siguientes campos:

```env
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USER=correoejemplo123@gmail.com
SMTP_PASS="aaaa bbbb cccc dddd"
MAIL_FROM_NAME="Sistema Web - Informe de Encuestas de Vuelos"
MAIL_TO=correoejemplo123receptor@gmail.com
CONTACT_EMAIL=info@correoejemploreceptor.es
CONTACT_PHONE=+34922001122
NOTIFICACION_TIEMPO=3
```

IMPORTANTE: Debes usar una cuenta de Gmail como `SMTP_USER` para que el envío de correos funcione correctamente. 
La variable `SMTP_PASS` no es tu contraseña habitual, sino una clave de servicio (App Password) que puedes generar desde la configuración de seguridad de tu cuenta Gmail (requiere tener activada la verificación en dos pasos). 
El formato suele ser como: "aaaa bbbb cccc dddd".

---

### 6. Usuarios de prueba

La aplicación cuenta con 2 usuarios de prueba:

- **usuario:** `usuario1` **contraseña:** `usuario1`
- **usuario:** `usuario2` **contraseña:** `usuario2`

---

### 7. Listo

Recuerda que la carpeta del proyecto debe estar en la carpeta `htdocs` de Apache.

Ya deberías poder acceder y utilizar la aplicación desde tu navegador.  

Si usas XAMPP, WAMP u otro servidor local que sirve desde htdocs, accede a `http://localhost/flight-survey-management-app`.
Si estás usando otro puerto, por ejemplo el `8080`, deberías acceder a `http://localhost:8080/flight-survey-management-app`.

Si al acceder a esa URL no se carga automáticamente el archivo `index.php`, prueba a acceder directamente a `http://localhost/flight-survey-management-app/public/index.php`.

---

## Autor

Desarrollado por [deritf](https://github.com/deritf)
