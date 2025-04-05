<?php

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? ''
);

// Railway asigna un puerto dinámico en la variable de entorno `PORT`.
// Usamos esta variable para que Laravel sirva la aplicación correctamente.
$port = getenv("PORT") ?: 8000;  // Si no se encuentra la variable, usa el puerto 8000 por defecto.

if ($uri !== '/' && file_exists(__DIR__.'/public'.$uri)) {
    return false;
}

require_once __DIR__.'/public/index.php';
