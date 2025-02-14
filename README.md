<!DOCTYPE html>
<html lang="es">
<body>
  <h1>Guía de Ejecución del Proyecto</h1>
  <p>
    Esta guía es un paso a paso para lograr ejecutar el backend del proyecto decameron
  </p>

  <h2>1. Requisitos Previos</h2>
  <p>
    Antes de comenzar, asegúrate de tener instaladas las siguientes herramientas:
  </p>
  <ul>
    <li>
      <strong>Docker Desktop para Windows</strong><br>
      Descárgalo e instálalo desde 
      <a href="https://www.docker.com/products/docker-desktop" target="_blank">Docker Desktop</a>.
      Docker te permite ejecutar el proyecto en contenedores, que son como pequeñas "cajas" con todo lo necesario para la aplicación.
    </li>
    <li>
      <strong>Composer</strong><br>
      Composer es el gestor de dependencias de PHP. Descárgalo desde 
      <a href="https://getcomposer.org/" target="_blank">getcomposer.org</a> y sigue las instrucciones de instalación.
    </li>
  </ul>

  <h2>2. Preparar el Proyecto</h2>
  <ol>
    <li>
      <strong>Clona o descarga el proyecto</strong><br>
      Clónalo usando Git o descárgalo como ZIP y extrae el contenido en una carpeta de tu computadora. (git clone https://github.com/JohanVacca/decameronback.git)
    </li>
    <li>
      <strong>Abre la terminal y navega a la carpeta del proyecto</strong><br>
      Por ejemplo:
      <pre><code>cd "C:\ruta\al\proyecto"</code></pre>
    </li>
    <li>
      <strong>Instala las dependencias de PHP</strong><br>
      Ejecuta el siguiente comando para instalar todas las dependencias definidas en <code>composer.json</code>:
      <pre><code>composer install</code></pre>
    </li>
  </ol>

  <h2>3. Configurar el Ambiente</h2>
  <ol>
    <li>
      <strong>Crear el archivo de configuración</strong><br>
      Dentro de la carpeta del proyecto, copia el archivo <code>.env.example</code> y renómbralo a <code>.env</code>.
    </li>
    <li>
      <strong>Editar las variables de entorno</strong><br>
      Abre el archivo <code>.env</code> y, si es necesario, modifica las variables (por ejemplo, <code>DB_DATABASE</code>, <code>DB_USERNAME</code> y <code>DB_PASSWORD</code>) según tus preferencias, puedes dejarlo tal cual.
    </li>
  </ol>

  <h2>4. Levantar los Contenedores con Docker</h2>
  <ol>
    <li>
      <strong>Abre DockerDesktop y verifica que esté corriendo</strong> en tu PC.
    </li>
    <li>
      <strong>Ejecuta Docker Compose</strong><br>
      Desde la carpeta del proyecto (donde se encuentra el archivo <code>docker-compose.yml</code>), ejecuta:
      <pre><code>docker-compose up --build -d</code></pre>
      Este comando construirá y levantará los contenedores necesarios:
      <ul>
        <li><strong>db:</strong> Contenedor para la base de datos (PostgreSQL).</li>
        <li><strong>app:</strong> Contenedor para la aplicación Laravel.</li>
      </ul>
    </li>
  </ol>

  <h2>5. Configurar la Aplicación Dentro del Contenedor</h2>
  <ol>
    <li>
      <strong>Accede al contenedor de la aplicación</strong><br>
      Ejecuta el siguiente comando para ingresar al contenedor llamado <code>decameron_laravel_app</code>:
      <pre><code>docker exec -it decameron_laravel_app bash</code></pre>
    </li>
    <li>
      <strong>Genera la clave de la aplicación</strong><br>
      Una vez dentro del contenedor, ejecuta:
      <pre><code>php artisan key:generate</code></pre>
      Esto configurará la clave necesaria para que la aplicación funcione correctamente.
    </li>
    <li>
      <strong>Ejecuta migraciones</strong><br>
      Una vez dentro del contenedor, ejecuta:
      <pre><code>php artisan migrate:fresh --seed</code></pre>
      Esto pondrá a correr las migraciones necesarias para poblar la base de datos.
    </li>
  </ol>

  <h2>6. Verificar que Todo Funcione</h2>
  <p>
    Abre tu navegador y visita 
    <a href="http://localhost:8000" target="_blank">http://localhost:8000</a>. 
    Si todo está configurado correctamente, deberías ver la página principal del proyecto.
  </p>

  <h2>Resumen de Comandos Clave</h2>
  <ul>
    <li>
      <strong>Instalar dependencias de PHP:</strong>
      <pre><code>composer install</code></pre>
    </li>
    <li>
      <strong>Levantar contenedores de Docker:</strong>
      <pre><code>docker-compose up --build -d</code></pre>
    </li>
    <li>
      <strong>Acceder al contenedor y generar la clave:</strong>
      <pre><code>docker exec -it decameron_laravel_app bash
php artisan key:generate</code></pre>
    </li>
    <li>
      <strong>Dentro del mismo contenedor sin salirte ejecuta las migraciones:</strong>
      <pre><code>docker exec -it decameron_laravel_app bash
php artisan migrate:fresh --seed</code></pre>
    </li>
  </ul>

  <p>
    Con estos pasos, hasta mi abuelita podrá poner en marcha el proyecto sin complicaciones :)
  </p>
</body>
</html>
