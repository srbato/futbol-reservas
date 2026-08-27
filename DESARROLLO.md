# TuCancha — Guía de desarrollo

Todo el entorno corre en Docker. No hace falta instalar PHP, PostgreSQL ni Node
en tu máquina.

---

## 1. Requisitos

| Herramienta | Notas |
|---|---|
| **Docker Desktop** | https://www.docker.com/products/docker-desktop — durante la instalación dejá tildado *"Use WSL 2 based engine"* |
| **WSL 2** (solo Windows) | Ver abajo |
| **Git** | Para clonar el repo |

### Windows: instalar WSL 2

Abrí **PowerShell como administrador** y ejecutá:

```powershell
wsl --install
```

Reiniciá la PC cuando termine. Eso instala Ubuntu, que vas a usar como terminal.

> **Importante:** trabajá siempre **dentro** de WSL, no en `C:\`.
> Los archivos en `/mnt/c/...` se leen 10 veces más lento desde Docker y el
> hot reload no funciona bien. Es la causa #1 de "me anda todo lentísimo".

---

## 2. Levantar el proyecto

Abrí la terminal de **Ubuntu** (no PowerShell) y ejecutá:

```bash
# Clonar dentro del home de WSL, NO en /mnt/c
mkdir -p ~/proyectos && cd ~/proyectos
git clone https://github.com/srbato/futbol-reservas.git
cd futbol-reservas

# Levantar todo
docker compose up -d
```

La primera vez tarda entre 5 y 10 minutos (compila PHP con sus extensiones e
instala dependencias). Las siguientes veces arranca en segundos.

Para seguir el progreso:

```bash
docker compose logs -f app
```

Cuando veas `✅ TuCancha listo` ya está funcionando.

### Cargar los datos de prueba

La base arranca vacía. Para tener complejos, canchas, reservas y partidos:

```bash
docker compose exec app php artisan migrate:fresh --seed
```

---

## 3. Accesos

| Qué | Dónde |
|---|---|
| La aplicación | http://localhost:8000 |
| Panel de administración | http://localhost:8000/sa |
| Base de datos | `localhost:5433` · base `tucancha` · usuario `tucancha` · contraseña `secret` |
| Websockets (Reverb) | `localhost:8080` |
| Vite (hot reload) | http://localhost:5173 |

### Usuarios de prueba

Todos tienen la contraseña **`password`**.

| Email | Rol |
|---|---|
| `srbattini@gmail.com` | Super admin (ve todo) |
| `usuario1@test.com` | Dueño de complejo |
| `usuario2@test.com` | Empleado del complejo |
| `usuario3@test.com` … `usuario5@test.com` | Jugadores |

---

## 4. Comandos del día a día

```bash
# Arrancar / parar
docker compose up -d
docker compose down

# Ver logs
docker compose logs -f app        # aplicación
docker compose logs -f queue      # emails y notificaciones
docker compose logs -f vite       # compilación de assets

# Consola dentro del contenedor
docker compose exec app bash

# Artisan (desde afuera)
docker compose exec app php artisan migrate
docker compose exec app php artisan tinker

# Tests
docker compose exec app php artisan test
docker compose exec app php artisan test --filter=FaltaUno

# Resetear la base con datos de prueba frescos
docker compose exec app php artisan migrate:fresh --seed
```

---

## 5. Probar pagos y login con Google

Mercado Pago y el login con Google **no funcionan contra `localhost`**, y no es
un problema de Docker:

- Mercado Pago rechaza `localhost` en las URLs de retorno y necesita poder
  alcanzar tu máquina desde internet para mandar los webhooks de pago.
- Google sólo redirige a URLs previamente registradas en su consola.

Para el 95% del trabajo diario no hacen falta: iniciás sesión con los usuarios
de prueba (email y contraseña) y reservás en el **Complejo de Usuario1**, que
está configurado para aceptar pago en efectivo.

Cuando sí necesites probarlos, exponé el puerto 8000 con [ngrok](https://ngrok.com):

```bash
ngrok http 8000
```

Copiá el dominio que te da (algo como `https://xxxx.ngrok-free.dev`) y agregalo
al final de tu `.env`:

```env
PUBLIC_URL=https://xxxx.ngrok-free.dev
```

Recreá los contenedores para que tomen el valor:

```bash
docker compose up -d
```

Con eso, `APP_URL`, las URLs de Mercado Pago y el callback de Google pasan a
apuntar al dominio público. Entrá por la URL de ngrok, no por localhost.

> Además hay que registrar `https://xxxx.ngrok-free.dev/auth/google/callback`
> en Google Cloud Console, y usar credenciales **de prueba** de Mercado Pago.
> El dominio de ngrok cambia cada vez que lo reiniciás (en el plan gratuito),
> así que hay que repetir estos pasos.

Cuando termines, borrá o comentá la línea `PUBLIC_URL` y volvé a levantar para
seguir trabajando en localhost.

---

## 6. Cómo trabajamos (GitFlow)

```
main       → producción. Lo que se mergea acá se deploya solo.
develop    → integración. Base de todo el trabajo diario.
feature/*  → una rama por funcionalidad.
hotfix/*   → arreglos urgentes de producción.
```

### Empezar una funcionalidad

```bash
git checkout develop
git pull origin develop
git checkout -b feature/nombre-descriptivo
```

Trabajás, commiteás, y cuando está lista:

```bash
git push -u origin feature/nombre-descriptivo
```

Después abrís un **Pull Request hacia `develop`** en GitHub. Los tests corren
automáticamente; si fallan, no se puede mergear.

### Publicar a producción

Cuando `develop` está estable, se abre un PR de `develop` → `main`.
Al mergearlo, el deploy corre solo (ver sección 7).

> **Nunca pushees directo a `main` ni a `develop`.** Siempre por PR.

### Arreglo urgente en producción

```bash
git checkout main
git pull origin main
git checkout -b hotfix/descripcion-corta
# ... arreglás ...
git push -u origin hotfix/descripcion-corta
```

PR a `main`, y después otro PR de `main` → `develop` para que el arreglo no se
pierda en el próximo release.

---

## 7. Deploy

El deploy es automático: **todo lo que llega a `main` se publica solo** en
https://tucancha.com.ar.

No hay que conectarse por SSH ni correr comandos a mano. El proceso hace backup
de la base, actualiza el código, corre migraciones y reinicia los servicios.

---

## 8. Problemas comunes

**"Cannot connect to the Docker daemon"**
Docker Desktop no está abierto. Abrilo y esperá a que el ícono deje de moverse.

**El sitio carga sin estilos**
El contenedor de Vite todavía está instalando dependencias la primera vez.
Mirá `docker compose logs -f vite` y esperá a que diga `ready in ...ms`.

**Los cambios en el código no se ven**
Verificá que clonaste el proyecto **dentro de WSL** (`~/proyectos/...`) y no en
`/mnt/c/...`.

**El puerto 8000 (o 5433) está ocupado**
Tenés otra cosa usando ese puerto. Cambiá el número de la izquierda en
`docker-compose.yml` (por ejemplo `8001:80`) y volvé a levantar.

**Quiero empezar de cero**
```bash
docker compose down -v   # -v borra también la base de datos
docker compose up -d --build
docker compose exec app php artisan migrate:fresh --seed
```

**Los emails no llegan**
Es a propósito: en desarrollo se escriben en `storage/logs/laravel.log` en lugar
de enviarse. Buscá el contenido del mail ahí.
