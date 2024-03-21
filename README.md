## TO DO:

No he conseguido que se cree un usuario de administrador desde el codigo sql cuya contraseña esté hasheada con bcrypt.
En consecuencia, hay que crear desde `register.php` un usuario con email "admin@admin.com" para acceder a los paneles de administración de la web.
En el código se restringe el acceso mediante `$_SESSION["customer"]["email"] == "admin@admin.com"`.

Lo suyo sería hacerlo con `.htaccess`, pero lo dejo como futura tarea.

Otra opción puede ser, en vez de tener un usuario "admin" de la tabla "customers" con "privilegios" para acceder a cierto contenido de la web que los demas "customers" no pueden ver, usar un sistema de roles que no esté hardcodeado en el código. Es decir, añadir una columna de ROL en la tabla "customers".

## Fichero de la base de datos exportado
Para probar la aplicación, en el repositorio se incluye un fichero con algunas categorias, productos, pedidos y clientes.
La contraseña de acceso de cada cliente es el nombre en minúsculas.
Por ejemplo:
- email: pepe@pepe.com --> password: pepe
- email: admin@admin.com --> password: admin
