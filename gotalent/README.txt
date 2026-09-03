GO TALENT - Instrucciones de instalación
=========================================

1) BASE DE DATOS
   - Ya tenés las tablas `participantes` y `jurado` cargadas (tal cual
     los dumps que me pasaste).
   - Entrá a phpMyAdmin, elegí la base `conakwwi_mantenimiento` y corré
     el archivo sql/actualizar_bd.sql (pestaña "SQL", pegás el contenido
     y ejecutás). Esto crea la tabla `votos`, que es la que impide que
     un jurado vote dos veces al mismo participante y permite sumar los
     puntajes de los 5.

2) CARGAR LOS 5 JURADOS
   - Abrí sql/crear_jurados.php con un editor de texto y completá el
     nombre, usuario y contraseña de cada uno de los 5 jurados.
   - Subí ese archivo al hosting y abrilo una vez desde el navegador
     (ej: https://tudominio.com/gotalent/sql/crear_jurados.php).
   - Te va a confirmar el alta de cada jurado con su contraseña ya
     encriptada en la base (nunca se guarda en texto plano).
   - IMPORTANTE: borrá sql/crear_jurados.php del servidor después de
     usarlo, para que nadie pueda volver a correrlo.

3) DATOS DE CONEXIÓN
   - Abrí config.php y completá DB_USER y DB_PASS con tu usuario y
     contraseña de MySQL (los mismos con los que entrás a phpMyAdmin).
   - Cambiá también ADMIN_PASS por una clave propia: es la que vas a
     usar para entrar a resultados.php.

4) SUBIR LOS ARCHIVOS
   - Subí toda la carpeta gotalent/ (o su contenido) a tu hosting, por
     ejemplo a public_html/gotalent/.

5) CÓMO SE USA
   - Cada jurado entra a login.php con su usuario y contraseña.
   - Ve la lista de participantes (lista.php), que es la misma para
     los 5. Toca "Votar" en el que quiera calificar.
   - Se abre la ficha del participante (votar.php) con talento, nombre,
     documento, localidad, teléfono, email y el campo de puntaje
     (1 a 10). Al confirmar, ese jurado ya no puede volver a calificar
     a ese mismo participante: la lista le va a mostrar "Calificado".
   - Los otros 4 jurados siguen viendo a ese participante como
     pendiente hasta que voten ellos también.
   - En resultados.php (con la clave de administración) ves, por cada
     participante, cuántos de los 5 jurados ya votaron y la suma total
     de los puntajes.

ESTRUCTURA DE ARCHIVOS
  config.php          -> conexión a la base y datos generales
  login.php            -> ingreso de jurados
  logout.php           -> cierre de sesión
  lista.php             -> listado compartido de participantes
  votar.php             -> ficha de votación de un participante
  resultados.php     -> panel con los puntajes sumados (solo organización)
  css/style.css        -> estilos de todo el sitio
  sql/actualizar_bd.sql -> crea la tabla votos
  sql/crear_jurados.php -> alta única de los 5 jurados (borrar después de usar)
