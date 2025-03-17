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

2. Configura el archivo `.env`: 

   2.1 Copia el archivo `.env.example` y renómbralo como `.env`.

   2.2 Abre el archivo `.env` y ajusta los valores de configuración según tus necesidades, utilizando la información requerida.

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

3. Ejecutar la aplicación:

   Para ejecutar el proceso de formateo de ventas, usa:

   ```
   php index.php
   ```
