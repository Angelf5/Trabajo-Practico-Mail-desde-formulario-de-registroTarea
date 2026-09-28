# Trabajo Práctico: Registro de Usuarios y Mailer Brevo SMTP en Laravel 13

**Asignatura:** Programación IV  
**Carrera:** Tecnicatura Universitaria en Programación

Sistema de registro de usuarios en Laravel 13 integrado con el servicio SMTP de Brevo. La aplicación valida los datos del formulario, registra al usuario en la base de datos y envía un correo HTML de bienvenida usando colas (Queues) para no bloquear la respuesta al usuario.

---

## Actividades realizadas

### 1. Configuración en Brevo
- Verificación de un remitente en **Settings → Senders & Domains → Senders**.
- Generación de una **SMTP Key** en **Settings → SMTP & API → SMTP**.
- Servidor: `smtp-relay.brevo.com`, puerto `587` (TLS).

### 2. Configuración del entorno (.env)
Se configuraron las variables de correo para usar Brevo SMTP:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_USERNAME=tu_correo_registrado_en_brevo
MAIL_PASSWORD=tu_smtp_key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=remitente_verificado
MAIL_FROM_NAME="${APP_NAME}"

QUEUE_CONNECTION=database
```

> Se usa la **SMTP Key** de Brevo, no la API Key.

### 3. Clase Mailable
- `app/Mail/WelcomeUserMail.php`: define el asunto del correo y pasa el nombre del usuario a la vista.

### 4. Vistas Blade
- `resources/views/emails/welcome.blade.php`: plantilla HTML del correo de bienvenida con el nombre dinámico del usuario.
- `resources/views/auth/register.blade.php`: formulario de registro con muestra de errores (`@error`) y retención de valores (`old()`).

### 5. Controlador con validaciones
`app/Http/Controllers/RegisterController.php` valida:
- **Nombre:** obligatorio, texto, entre 3 y 255 caracteres.
- **Email:** obligatorio, formato RFC/DNS, máximo 255 caracteres, único en la tabla `users`.
- **Contraseña:** obligatoria, mínimo 8 caracteres, con confirmación.

Incluye mensajes de error personalizados en español. Luego guarda el usuario con la contraseña encriptada y despacha el envío del correo.

### 6. Rutas
En `routes/web.php`:
- `GET /register` → muestra el formulario.
- `POST /register` → procesa el registro.

### 7. Colas (Queues)
- Se configuró `QUEUE_CONNECTION=database` para guardar los trabajos pendientes en la tabla `jobs`.
- Se creó un **Job dedicado** `app/Jobs/SendWelcomeEmailJob.php` que envía el correo en segundo plano.
- El controlador despacha el Job con `SendWelcomeEmailJob::dispatch($user)`.
- Así el formulario responde de inmediato, y el envío a Brevo lo hace el worker por separado.

---

## Instalación y ejecución

```bash
git clone https://github.com/Angelf5/Trabajo-Practico-Mail-desde-formulario-de-registroTarea.git
cd Trabajo-Practico-Mail-desde-formulario-de-registroTarea
composer install
cp .env.example .env
php artisan key:generate
```

Completar las credenciales de Brevo y la base de datos en el `.env`, y luego:

```bash
php artisan migrate
php artisan serve
```

En otra terminal, iniciar el procesador de colas:

```bash
php artisan queue:work
```

Abrir en el navegador: http://127.0.0.1:8000/register

---

## Pruebas realizadas
1. Envío del formulario vacío: se muestran los mensajes de validación.
2. Registro con un email ya existente: se muestra "Este correo electrónico ya se encuentra registrado."
3. Contraseñas distintas: se muestra "Las contraseñas ingresadas no coinciden."
4. Registro con datos válidos: el usuario se guarda en la base de datos y llega el correo de bienvenida.

## Gestión de trabajos fallidos

```bash
php artisan queue:failed      # Ver trabajos fallidos
php artisan queue:retry {id}  # Reintentar uno
php artisan queue:retry all   # Reintentar todos
```