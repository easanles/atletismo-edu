Sistema de gestión de un club de atletismo
==========================================

El objetivo principal de este proyecto es disponer de una alternativa basada en Software Libre para la gestión de las tareas administrativas de un club de atletismo.

La aplicación permite realizar lo siguiente:

*    Gestionar los socios del club: altas, mantenimiento y bajas de los miembros del club.
*    Controlar las cuotas de los socios del club.
*    Controlar las inscripciones de los atletas en las pruebas recomendadas por el club.
*    Registrar las marcas de los atletas para cada una de las pruebas en las que participe.
*    Gestión de contabilidad de las cuotas de los asociados simplificada (ingresos/gastos).
*    La aplicación es completamente gestionable a través de una interfaz web.

Instalación
-----------

 1. Disponga de un **servidor web** con interprete de **PHP** y un sistema de gestión de bases de datos instalado.
Se necesita al menos la versión **5.5.9** de PHP. Las extensiones de PHP, **JSON** y **ctype** deben estar activos. **PDO** debe estar instalado. El archivo de configuración php.ini necesita tener ajustada la variable `date.timezone`.
También se necesita **Composer** para realizar la instalación y la comprobación de dependencias.
 2. Descomprima los contenidos en una localización accesible desde un navegador.
 3. Cree una base de datos vacía en el sistema gestor de bases de datos que esté utilizando. Por defecto el nombre utilizado para esa base de datos es `easanlesAtletismo`.
 4. Acceda a la carpeta descomprimida desde un terminal y ejecute
el comando `composer install`.
 5. Rellene las variables de configuración de la base de datos.
 6. Acceda desde un navegador web a la dirección */web/config.php* . Corrija los problemas de configuración del servidor. En la mayoría de casos indicará un problema con los permisos de los directorios *app/cache* y *app/logs*. Ajuste los permisos para que el servidor
web pueda escribir en estos directorios, y borre o ajuste los permisos de todos los archivos contenidos en ambas carpetas. Ajuste también los permisos de la carpeta *web* para que la aplicación pueda crear los assets de la aplicación.
 7. Pulse *“Re-check configuration”* para comprobar si la aplicación está configurada correctamente. 
 8. Acceda a *web/app_dev.php* desde la misma localización en la que está alojado el servidor web. Haga clic en *“Crear base de datos”*. Cuando el proceso termine y se indique el mensaje *“La base de datos está correctamente configurada”* la aplicación estará finalmente lista para ser usada. Acceda al entorno de producción entrando en la dirección *web/app.php*.

