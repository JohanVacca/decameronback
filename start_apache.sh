#!/bin/sh

# Imprimir mensaje de inicio
echo "BACKEND METROPOLIS: Iniciando Apache..."

# Iniciar Apache en segundo plano
apache2-foreground &

# Esperar a que Apache esté listo
echo "Esperando a que Apache esté listo..."
while ! nc -z localhost 80; do
    echo "Apache no está listo, esperando 3 segundos..."
    sleep 3 # esperar 3 segundos antes de volver a intentar
done

# Apache está listo
echo "Apache está listo."

# Ejecutar migraciones y seeders
# echo "Ejecutando migraciones y seeders..."
# if ! php artisan migrate:fresh --seed; then
#     echo "Error al ejecutar migraciones y seeders."
# fi

# Imprimir mensaje de finalización
echo "Script completado con éxito o terminó con errores manejados."

# Mantener el contenedor corriendo
tail -f /dev/null
