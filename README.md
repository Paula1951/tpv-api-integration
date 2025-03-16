# Simulación de Integración con API TPV

Este proyecto simula la conexión con una API de TPV y procesa las ventas en un formato estandarizado.

## Requisitos del sistema
Antes de comenzar, asegúrate de tener instalado lo siguiente:

### Sistema Operativo
- **Windows 10 o superior** (Recomendado)

### Requisitos adicionales
- PHP >= 8.0
- Composer

## Instrucciones

1. Instala las dependencias:
   Ejecuta el siguiente comando para instalar todas las dependencias necesarias:

   ```bash
   composer install
   ```

2. Confiura el archivo .env:
   Crea y configura un archivo .env con la siguiente información:

   ```
   SERVER=tu_direccion_servidor
   PORT=tu_puerto_servidor
   API_TOKEN=tu_token_api
   ENDPOINT_TICKET=tu_endpoint_tickets
   ```

   Ejemplo:
   ```
   SERVER=https://ejemplo.com
   PORT=8080
   API_TOKEN=abc123xyz
   ENDPOINT_TICKET=/api/tickets
   ```

3. Ejecutar la Aplicación
   Para ejecutar el proceso de formateo de ventas, usa:

   ```
   php index.php
   ```
