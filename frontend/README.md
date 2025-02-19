# PRACTICA UD 2

Este proyecto está compuesto con dos archivos: backend y frontend.
Backend está hecho con laravel(php) para procesar las solicitudes. Los datos se guardan en archivos.
Uso de rutas api para comunicar backend con frontend
En el frontend uso de HTML y CSS para la estructura y estilo de la pagina. Javascript para la comunicaion con el backend.
El frontend envia datos al backend, Laravel procesa las solicitudes y las guarda en los archivos

## TECNOLOGIAS EMPLEADAS
### BACKEND 
Laravel, PHP, Composer

### FRONTEND
HTML, CSS
JavaScript

### SECUENCIA PARA LEVANTAR EL ENTORNO
Instalar php, composer.
git clone 
cd practicaUD2
cd backend 
composer install -n --prefer-dist
cp .env.example .env
php artisan key:generate

Configurar los controllers para pasar los test
php artisan serve

En frontend almacenas la url del backend para las solicitudes
Con fetch haces la solicitudes
Abrir el html en el navegador, port 5500 default
