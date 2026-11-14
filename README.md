# API de taquitos

Proyecto escolar para practicar una API REST con Laravel y SQLite. La API administra un catalogo de taquitos: permite listarlos, registrar uno, consultar uno, actualizarlo y eliminarlo.

> Este proyecto es un punto de partida para aprender. Cambia los nombres, precios, descripciones y decisiones del menu para que representen tu propia taqueria. Debes poder explicar las decisiones que entregues.

## 1. Que necesitas

El proyecto funciona en el entorno Linux (Debian) de tu Chromebook. VS Code es donde editas; la terminal integrada de VS Code ejecuta los comandos de Laravel; SQLite guarda los datos; Beekeeper Studio te permite inspeccionar visualmente esa base.

- PHP 8.3 o posterior.
- Composer.
- La extension `pdo_sqlite` de PHP.
- Git para subir el proyecto.
- Beekeeper Studio es opcional: Laravel ya puede trabajar con SQLite sin abrir una aplicacion visual.

Para comprobar PHP, Composer y SQLite:

```bash
php -v
composer -V
php -m | grep -E 'pdo_sqlite|sqlite3'
```

En Debian, si aun te falta la mayoria de los paquetes de PHP, puedes instalar los basicos asi:

```bash
sudo apt update
sudo apt install php-cli php-mbstring php-xml php-bcmath php-sqlite3 unzip curl git
```

En esta computadora ya comprobamos PHP, Composer y SQLite, pero el comando `git` no esta instalado. Antes de subir a GitHub, instala Git y verifica que quede disponible:

```bash
sudo apt update
sudo apt install git
git --version
```

Este proyecto ya requiere Composer y tiene sus dependencias en `composer.json` y `composer.lock`. Si Composer no esta instalado, instala Composer siguiendo su sitio oficial. No ejecutes comandos de instalacion desde paginas que no hayas verificado.

## 2. Abrir la carpeta en VS Code

En una terminal de Debian, ve a la carpeta del proyecto y abre VS Code:

```bash
cd /home/alangps890/taquitos-api
code .
```

Si `code .` no funciona, abre VS Code desde el menu del Chromebook y elige **File > Open Folder** para abrir `/home/alangps890/taquitos-api`.

Abre la terminal integrada de VS Code desde **Terminal > New Terminal**. Los comandos de esta guia se ejecutan desde la carpeta principal del proyecto, donde estan `artisan` y `composer.json`. Puedes comprobarlo con:

```bash
pwd
ls
```

## 3. Preparar Laravel por primera vez

Ejecuta estos pasos desde la terminal integrada:

```bash
composer install
test -f .env || cp .env.example .env
```

`composer install` instala las bibliotecas fijadas por el proyecto. El archivo `.env` guarda configuracion propia de tu computadora y no se debe subir a GitHub.

En una copia nueva del proyecto, genera la clave de Laravel una sola vez:

```bash
php artisan key:generate
```

No vuelvas a generar la clave en una instalacion que ya tenga `APP_KEY` en `.env`, a menos que sepas por que necesitas cambiarla.

Comprueba que `.env` use SQLite:

```dotenv
DB_CONNECTION=sqlite
```

Laravel toma por defecto la base de datos de `database/database.sqlite`. Si ese archivo todavia no existe, crealo antes de las migraciones:

```bash
touch database/database.sqlite
```

Luego crea las tablas y carga los datos de ejemplo:

```bash
php artisan migrate --seed
```

Una **migracion** define la estructura de una tabla. El **seeder** agrega algunos registros de muestra. Si Artisan pregunta si deseas crear la base de datos, responde que si.

> Las semillas de este proyecto pueden ejecutarse mas de una vez sin repetir los tres taquitos de ejemplo. No uses `migrate:fresh` en una base con datos que quieras conservar: ese comando borra las tablas y las vuelve a crear.

## 4. Iniciar y detener la API

En una terminal de VS Code:

```bash
php artisan serve
```

Deja esa terminal abierta mientras trabajas. Laravel mostrara una direccion, normalmente `http://127.0.0.1:8000`. Puedes comprobar que el proyecto responde abriendo esa direccion en el navegador; la pagina inicial de Laravel no es un endpoint de la API.

Para detener el servidor, vuelve a esa terminal y presiona `Ctrl+C`. Para ejecutar otros comandos, abre una segunda terminal integrada; no cierres la que esta ejecutando el servidor.

La URL base de la API es:

```text
http://127.0.0.1:8000/api
```

## 5. Entender que hace cada parte

Una API recibe una solicitud HTTP, realiza una accion y devuelve datos, normalmente en JSON. En este proyecto:

1. `routes/api.php` relaciona URLs y metodos HTTP con acciones del controlador.
2. `StoreTaquitoRequest` y `UpdateTaquitoRequest` revisan los datos recibidos.
3. `TaquitoController` coordina la lectura, creacion, actualizacion y eliminacion.
4. El modelo `Taquito` representa los registros de la tabla y los consulta con Eloquent, el ORM de Laravel.
5. `TaquitoResource` decide que campos se devuelven en JSON.
6. La migracion crea la tabla `taquitos` en SQLite.

La tabla contiene:

| Campo | Para que sirve |
|---|---|
| `id` | Identificador unico generado por la base de datos |
| `name` | Nombre del taquito |
| `description` | Descripcion; puede quedar vacia |
| `price` | Precio decimal con dos posiciones |
| `category` | Categoria o tipo de carne |
| `is_available` | Indica si se puede vender |
| `created_at`, `updated_at` | Fechas que Laravel mantiene automaticamente |

**CRUD** son las cuatro operaciones basicas de datos: Create (crear), Read (leer), Update (actualizar) y Delete (eliminar).

## 6. Probar los endpoints

Puedes instalar la extension **Thunder Client** desde la seccion Extensions de VS Code, o usar Postman. En Thunder Client crea una solicitud, selecciona el metodo, escribe la URL completa y, para `POST`, `PUT` o `PATCH`, elige **Body > JSON**.

### Listar todos

```http
GET http://127.0.0.1:8000/api/taquitos
```

Devuelve hasta 10 taquitos por pagina. La respuesta incluye `data` con los registros, y `links` y `meta` con informacion de paginacion. Para ver otra pagina, prueba `?page=2`.

### Crear uno

```http
POST http://127.0.0.1:8000/api/taquitos
Content-Type: application/json
```

Cuerpo JSON:

```json
{
  "name": "Taco al pastor",
  "description": "Con piña, cilantro y cebolla",
  "price": 25.5,
  "category": "Pastor",
  "is_available": true
}
```

`name`, `price` y `category` son obligatorios. `description` se puede omitir o dejar como `null`; si omites `is_available`, el taquito queda disponible. Si los datos son validos, la API responde con estado **201 Created** y el taquito creado.

### Consultar uno

Primero usa el listado para encontrar el `id` real y reemplaza `{id}` en la URL:

```http
GET http://127.0.0.1:8000/api/taquitos/{id}
```

### Actualizar uno

Para cambiar solo algunos campos, usa `PATCH`. Por ejemplo:

```http
PATCH http://127.0.0.1:8000/api/taquitos/{id}
Content-Type: application/json
```

Cuerpo:

```json
{
  "price": 30,
  "is_available": false
}
```

Puedes enviar `PUT` o `PATCH`; en esta implementacion los dos permiten actualizar solo los campos que mandes. Si el registro no existe, recibes una respuesta JSON **404 Not Found**.

### Eliminar uno

```http
DELETE http://127.0.0.1:8000/api/taquitos/{id}
```

La respuesta confirma la eliminacion. Para verificarla, vuelve a consultar el listado.

### Validaciones y errores

- El nombre es texto de hasta 120 caracteres.
- El precio debe ser numerico, mayor que cero y no superar `999999.99`.
- La categoria es texto de hasta 80 caracteres.
- La descripcion puede tener hasta 1000 caracteres.
- La disponibilidad debe ser un valor booleano (`true` o `false`).

Si los datos no cumplen las reglas, Laravel responde con estado **422 Unprocessable Content** y un JSON con `message` y `errors`, donde aparecen los errores por campo. Por ejemplo, intenta crear un taquito sin `name` o con precio `0` para observar la respuesta.

## 7. Ver los datos en Beekeeper Studio

1. Abre Beekeeper Studio y crea una conexion nueva de tipo **SQLite**.
2. En la seleccion de archivo, abre `/home/alangps890/taquitos-api/database/database.sqlite`.
3. Conecta y busca las tablas `taquitos`, `migrations` y las tablas de Laravel.
4. Abre `taquitos` para ver sus filas y columnas.
5. Si acabas de crear registros desde la API, refresca la vista de Beekeeper.

Beekeeper es un cliente visual, no el servidor de base de datos. Laravel y Beekeeper deben abrir el mismo archivo SQLite. No configures MySQL o MariaDB para este proyecto.

## 8. Comprobar que funciona con pruebas

En una segunda terminal de VS Code, ejecuta:

```bash
php artisan test
```

Las pruebas verifican el listado, la creacion, el rechazo de datos invalidos, la consulta, la actualizacion y la eliminacion. Usan una base SQLite temporal en memoria; no eliminan los datos de `database/database.sqlite`.

## 9. Subir el proyecto a GitHub

La carpeta actual todavia no tiene un repositorio Git ni esta conectada a GitHub. Primero confirma con tu profesor si debes usar un repositorio que ya creo o crear uno nuevo. Necesitas la URL HTTPS de ese repositorio para completar la subida.

### Si ya existe un repositorio vacio en GitHub

En GitHub copia la URL HTTPS del repositorio. En la terminal de VS Code, desde la carpeta del proyecto, ejecuta:

```bash
git init
git add .
git status
```

Antes de confirmar, revisa `git status`: `.env`, `vendor` y `database/database.sqlite` no deben aparecer como archivos listos para subir. `.env` tiene configuracion privada; la base SQLite local contiene datos de desarrollo. El archivo `.gitignore` esta preparado para excluirlos.

Si la lista es correcta, guarda el primer cambio y conecta el remoto:

```bash
git commit -m "Crear API de catalogo de taquitos"
git branch -M main
git remote add origin https://github.com/TU-USUARIO/TU-REPOSITORIO.git
git push -u origin main
```

Reemplaza la URL de ejemplo con la direccion real. GitHub puede pedirte autenticarte en el navegador. No pegues contrasenas, tokens ni claves privadas en el codigo o en un commit.

Si el repositorio de GitHub ya contiene un README, licencia u otros archivos, no sigas a ciegas con `git init` y el primer `push`: conviene clonar ese repositorio y mover tu proyecto a la copia, o pedir ayuda para integrar los historiales.

### Que entregar como evidencia

- Captura de Thunder Client con el `GET` que lista los taquitos y su respuesta JSON.
- Captura de un `POST` exitoso y su respuesta `201`.
- Captura de una validacion fallida que muestre errores `422`.
- Captura de Beekeeper mostrando la tabla `taquitos`.
- Captura de GitHub mostrando los archivos del repositorio.
- Si el profesor lo solicita, captura de `php artisan test` mostrando las pruebas aprobadas.

Evita que las capturas muestren contrasenas, tokens, el contenido de `.env` o informacion personal.

## 10. Archivos principales

- `routes/api.php`: define las rutas HTTP de la API.
- `app/Http/Controllers/Api/TaquitoController.php`: contiene las acciones del CRUD.
- `app/Http/Requests/StoreTaquitoRequest.php`: valida los datos para crear.
- `app/Http/Requests/UpdateTaquitoRequest.php`: valida los campos enviados al actualizar.
- `app/Http/Resources/TaquitoResource.php`: define la forma de cada taquito en JSON.
- `app/Models/Taquito.php`: representa y consulta un taquito.
- `database/migrations/`: define la estructura de las tablas.
- `database/factories/TaquitoFactory.php`: genera taquitos de ejemplo para pruebas.
- `database/seeders/DatabaseSeeder.php`: carga ejemplos al ejecutar `migrate --seed`.
- `tests/Feature/TaquitoApiTest.php`: comprueba los endpoints.

## Como explicar el proyecto con tus propias palabras

Practica una explicacion sencilla y fiel a lo que hace el codigo:

> “Es una API REST en Laravel para administrar un catalogo de taquitos. Guarda los datos en SQLite. Las rutas reciben solicitudes HTTP, el controlador ejecuta las operaciones con el modelo, las solicitudes validan la entrada y el recurso presenta la respuesta en JSON. Tambien tiene pruebas para comprobar el CRUD y las validaciones.”

No memorices la frase: abre los archivos, prueba cada endpoint y explica por que elegiste los datos y precios de tu menu. Si tu curso pide declarar el uso de herramientas de IA, sigue la regla del profesor.
