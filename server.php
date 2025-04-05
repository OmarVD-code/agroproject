<?php

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? ''
);

$port = getenv("PORT") ?: 8000;

// Verifica si el puerto se asignó correctamente
error_log("Servidor escuchando en el puerto: $port");  // Agregar un log de error temporal

if ($uri !== '/' && file_exists(__DIR__.'/public'.$uri)) {
    return false;
}

require_once __DIR__.'/public/index.php';
