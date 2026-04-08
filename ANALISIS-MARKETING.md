# TuCancha — Analisis Completo: Marketing, SEO, Competencia y Contenido
*Generado: 8 de abril 2026*

---

## Indice

1. [Audit Tecnico](#1-audit-tecnico)
2. [SEO Audit](#2-seo-audit)
3. [Competitive Brief](#3-competitive-brief)
4. [Campaign Plan](#4-campaign-plan)
5. [Draft Content](#5-draft-content)

---

## 1. Audit Tecnico

### Audit Health Score

| # | Dimension | Score | Key Finding |
|---|-----------|-------|-------------|
| 1 | Accessibility | 3 | Cards interactivas sin `aria-label`, stars sin texto alternativo |
| 2 | Performance | 2 | `transition: all` en 6+ archivos, `backdrop-filter` en elementos que scrollean, easings suboptimos en tokens |
| 3 | Theming | 2 | 18+ colores hardcodeados que deberian usar CSS variables |
| 4 | Responsive | 3 | Buena cobertura de breakpoints, 1 altura fija problematica en welcome |
| 5 | Anti-Patterns | 3 | Sin AI slop, pero easings custom duplicados y un serif generico |
| **Total** | | **13/20** | **Acceptable — trabajo significativo en theming y performance** |

### Anti-Patterns Verdict

**Pass.** No parece AI-generated. Sin gradient text, sin neon, sin glassmorphism generico, sin 3-col equal cards. Los problemas son tecnicos (hardcoded values, transitions), no de diseno.

### Top Issues by Severity

**[P1] `transition: all` en multiples archivos** — layouts/app.blade.php (4 instancias), vistas viejas. Causa repintado innecesario de propiedades que no cambian.
**Fix:** Reemplazar con propiedades especificas.

**[P1] 18+ colores hardcodeados** — `#111`, `#222`, `#4a4a4a`, `rgba(255,255,255,.95)` directos en vez de `var(--color-bg-dark)`, `var(--color-text)`, etc. Hace imposible tematizar.
**Fix:** Migrar todos a design tokens.

**[P1] Easings custom definidos localmente** — `--ease-out-expo` repetido en nosotros, create, public-show. Deberia estar en `design-tokens.css`.
**Fix:** Centralizar en tokens.

**[P2] `backdrop-filter: blur()` en header sticky** — marketing.blade.php y app.blade.php. Causa repintado continuo al scrollear en mobile.

**[P2] Transition tokens usan `ease` generico** — `design-tokens.css` define `--transition-fast: 150ms ease` pero deberia ser `ease-out` para UI.

**[P2] Cards interactivas sin ARIA** — Sport cards en create y public-show son clickeables pero sin `role` ni `aria-pressed`.

**[P3] Star ratings sin texto alternativo** — public-show.blade.php muestra SVG stars sin `aria-label` para screen readers.

### Positive Findings

- `prefers-reduced-motion` implementado en las 3 vistas rediseñadas
- Focus-visible rings en todas las vistas nuevas
- SVGs inline que no dependen de Lucide = siempre renderizan
- `{ passive: true }` en scroll listeners
- `font-variant-numeric: tabular-nums` en stats
- `clamp()` para tipografia fluida
- Double-bezel architecture consistente en las vistas nuevas

### Fixes Aplicados

**Extract:**
- Easings centralizados en `design-tokens.css` (`--ease-out-expo`, `--ease-out-quart`, `--ease-in-out-expo`, `--ease-drawer`)
- Eliminadas las definiciones locales duplicadas de 4 vistas

**Optimize:**
- 4x `transition: all` reemplazados con propiedades especificas en `app.blade.php`
- Transition tokens actualizados de `ease` generico a `var(--ease-out-expo)`
- Blob animations: `ease-in-out` a `var(--ease-out-quart)`
- Serif font generico eliminado del quote pseudo-element

**Harden:**
- `aria-label` en 5 sport cards de create.blade.php
- `aria-label` en sport cards de public-show.blade.php
- `role="img"` + `aria-label` en rating stars con `aria-hidden` en SVGs individuales
- Inline `onmouseover`/`onmouseout` reemplazados por CSS `:hover` en marketing.blade.php
- Feedback button usa `var(--color-bg-dark)` en vez de hardcoded

---

## 2. SEO Audit

### Executive Summary

TuCancha tiene una base tecnica solida (meta tags, OG, canonical, sitemap dinamico, robots.txt, imagenes WebP) pero le faltan los elementos que realmente mueven rankings: structured data (JSON-LD), contenido indexable (blog, guias), y diferenciacion de keywords vs competencia fuerte. La competencia directa (ATC/AlquilaTuCancha, DondeJuego, Canchas.club, QuieroCancha) tiene anos de ventaja en SEO y presencia en app stores.

**Fortaleza principal:** Stack tecnico limpio con meta tags bien implementados y sitemap dinamico.
**Top 3 prioridades:** 1) Agregar JSON-LD structured data, 2) Crear contenido indexable (blog/guias), 3) Optimizar la imagen hero de 1.7MB.

### Keyword Opportunity Table

| Keyword | Dificultad | Oportunidad | Ranking actual | Intent | Contenido recomendado |
|---|---|---|---|---|---|
| reservar cancha online | Alta | Media | No rankea | Transaccional | Landing page optimizada |
| cancha de futbol 5 cerca | Alta | Media | No rankea | Local/Trans. | Pagina de busqueda con geo |
| alquilar cancha de padel | Media | Alta | No rankea | Transaccional | Landing sport-specific |
| sistema de reservas canchas | Media | Alta | No rankea | Comercial | Landing B2B (duenos) |
| app para reservar cancha | Alta | Baja | No rankea | Navegacional | Dominado por ATC |
| como organizar partido de futbol | Baja | Alta | No rankea | Informacional | Blog post / guia |
| reserva cancha sin llamar | Baja | Alta | No rankea | Transaccional | Landing + blog |
| torneos de futbol amateur argentina | Baja | Alta | No rankea | Informacional | Pillar page (futuro) |
| software gestion complejo deportivo | Media | Alta | No rankea | Comercial | Landing B2B |
| cancha de tenis buenos aires | Media | Media | No rankea | Local | Pagina geo-segmentada |
| futbol amateur buscar jugadores | Baja | Alta | No rankea | Informacional | Landing Falta Uno |
| precio alquiler cancha futbol 5 | Baja | Alta | No rankea | Comercial | Blog + herramienta |
| cancha de padel categorias | Baja | Alta | No rankea | Informacional | Blog / FAQ |
| reservar cancha online gratis | Media | Media | No rankea | Transaccional | Landing (sin comisiones) |
| complejo deportivo gestion online | Baja | Alta | No rankea | Comercial | Landing B2B |

### On-Page Issues Table

| Pagina | Issue | Severidad | Fix recomendado |
|---|---|---|---|
| `/planes` | Sin meta_description | **Critical** | Agregar `@section('meta_description', '...')` |
| Todas | Sin JSON-LD structured data | **Critical** | Agregar Organization, WebSite, BreadcrumbList |
| `/` (home) | Hero image 1.7MB | **High** | Crear variantes responsive (400w, 800w, 1200w) |
| `/venues/{venue}` | Sin meta_description propia | **High** | Generar dinamicamente con nombre + deporte + zona |
| `/falta-uno` | Sin meta_description propia | **High** | Agregar descripcion del concepto Falta Uno |
| Guest layout | Sin meta tags SEO | **Medium** | Agregar description + OG al layout guest |
| Todas | Sin hreflang | **Low** | Agregar `<link rel="alternate" hreflang="es-AR">` |
| Todas | Sin breadcrumbs | **Medium** | Agregar breadcrumb nav + BreadcrumbList JSON-LD |

### Content Gap Recommendations

| Topic | Por que importa | Formato | Prioridad | Esfuerzo |
|---|---|---|---|---|
| "Como organizar un partido de futbol con amigos" | Alta busqueda, baja competencia, conecta con Falta Uno | Blog post | Alta | 2hs |
| "Guia de categorias de padel en Argentina" | Educacional, conecta con perfil deportivo | Guia | Alta | 3hs |
| "Cuanto cuesta alquilar una cancha de futbol 5" | Keyword comercial con intent claro | Blog + tabla precios | Alta | 2hs |
| "Software de gestion para complejos deportivos" | B2B keyword, capta duenos de complejos | Landing page | Alta | 4hs |
| "Diferencias entre canchas de futbol 5, 7 y 11" | Informacional, baja competencia | Blog post | Media | 1h |
| "Torneos de futbol amateur: como organizarlos" | Prepara terreno para feature de torneos | Pillar page | Media | 6hs |
| "Las mejores canchas de padel en [ciudad]" | Geo-targeting, atrae jugadores locales | Serie de posts | Media | 3hs/ciudad |
| FAQ interactivo | Captura "People Also Ask" en Google | Pagina FAQ + FAQ schema | Alta | 3hs |

### Technical SEO Checklist

| Check | Status | Detalle |
|---|---|---|
| HTTPS | **Pass** | Let's Encrypt configurado |
| Canonical tags | **Pass** | `url()->current()` en todos los layouts |
| Meta description | **Warning** | Faltaba en `/planes`, pages de venues, falta-uno |
| Sitemap XML | **Pass** | Dinamico con venues y fields |
| Robots.txt | **Pass** | Bloquea admin/profile, incluye sitemap |
| Open Graph | **Pass** | Implementado en marketing y app layouts |
| Twitter Cards | **Pass** | summary_large_image en todos |
| Favicon | **Pass** | SVG + PNG + apple-touch-icon |
| PWA manifest | **Pass** | Configurado correctamente |
| Structured data JSON-LD | **Fail** | No existia en ninguna vista |
| Breadcrumbs | **Fail** | No implementados |
| Image optimization | **Warning** | hero-cancha.webp = 1.7MB, necesita responsive |
| Mobile responsive | **Pass** | Breakpoints implementados |
| Page speed (images) | **Warning** | Sin lazy loading en hero, sin srcset |
| hreflang | **Fail** | No declarado (es-AR) |
| meta keywords | **Warning** | Existia pero Google lo ignora desde 2009 |

### Competitor SEO Comparison

| Dimension | TuCancha | ATC Sports | DondeJuego | Canchas.club |
|---|---|---|---|---|
| Anos activo | <1 ano | 8+ anos | 5+ anos | 3+ anos |
| App nativa | No | iOS + Android | iOS + Android | No |
| Blog/contenido | No | No | Si | No |
| Structured data | No | Parcial | Si | No |
| Paises | Argentina | 9 paises LATAM | Argentina | Argentina |
| Feature unico | Falta Uno + Torneos | Multi-pais, madurez | Cobertura ciudades | Simplicidad |
| SEO content | Bajo | Bajo | Medio | Bajo |

### Quick Wins Implementados

| Quick Win | Archivos modificados | Detalle |
|---|---|---|
| Meta descriptions | `planes.blade.php`, `falta-uno/index.blade.php` | Descriptions unicas para cada pagina |
| JSON-LD Organization | `marketing.blade.php`, `app.blade.php` | Schema.org Organization con name, logo, contact, area |
| JSON-LD WebSite | `marketing.blade.php` | SearchAction para search box en Google |
| JSON-LD BreadcrumbList | `marketing.blade.php` (layout) + `nosotros`, `como-funciona`, `planes` (data) | Breadcrumbs dinamicos con @push |
| Hero image responsive | 3 variantes creadas (29KB, 100KB, 192KB vs 1.7MB), `welcome.blade.php` con media queries | Mobile carga 29KB en vez de 1.7MB |
| Meta keywords eliminados | `marketing.blade.php`, `app.blade.php` | Google las ignora desde 2009 |

### Strategic Investments (este trimestre)

1. **Crear blog** en `/blog` con 5-8 posts iniciales targeting long-tail keywords
2. **Crear FAQ page** con FAQ schema markup
3. **Landing pages por deporte** (`/canchas-de-padel`, `/canchas-de-futbol-5`)
4. **Landing B2B** (`/para-complejos`) optimizada para "software gestion complejo deportivo"
5. **Geo-pages** por ciudad cuando haya venues suficientes

---

## 3. Competitive Brief

*Fecha de investigacion: 7 de abril 2026*

### Executive Summary

El mercado de reservas de canchas online en Argentina esta dominado por ATC Sports (8+ anos, presencia en 14 paises LATAM) y seguido por DondeJuego (cobertura multi-ciudad) y Canchas.club (modelo marketplace). Ningun competidor tiene las features de Falta Uno (buscar jugadores para partidos) ni Torneos (en desarrollo). Todos cobran comisiones o fees por transaccion excepto TuCancha.

**Mayor oportunidad:** El espacio de "social sports" (encontrar jugadores, armar equipos, rankings) esta completamente vacio. TuCancha con Falta Uno + Perfil Deportivo + Torneos puede posicionarse como la "red social deportiva" de Argentina, no solo una herramienta de reservas.

**Mayor amenaza:** ATC tiene masa critica (anos de operacion, apps nativas, 14 paises). Si agregan features sociales, compiten directamente. La ventaja de TuCancha es moverse rapido mientras ellos son lentos en innovar.

### Competitor Profiles

#### ATC Sports / AlquilaTuCancha
- **Que hace:** Software de gestion para complejos + marketplace de reservas para jugadores
- **Tagline:** "Tu proximo partido, a un link de distancia"
- **Target:** Complejos deportivos (B2B) + jugadores (B2C)
- **Tamano:** 8+ anos operando, 14 paises LATAM, se autodenominan "#1 en LATAM"
- **Pricing:** Fee mensual fijo para complejos (sin monto publico), 33% descuento anual
- **Deportes:** Futbol, padel, tenis, mas
- **Apps:** iOS + Android
- **Fortalezas:** Masa critica, presencia regional, apps nativas, anos de data, confianza establecida
- **Debilidades:** Sin features sociales (no hay "buscar jugadores"), UI anticuada, sin blog/contenido SEO, sin sistema de ratings/reputacion, sin torneos
- **Messaging:** Funcional, sin personalidad. "Reserva tu cancha" generico

#### DondeJuego
- **Que hace:** App de reservas enfocada en futbol y padel
- **Tagline:** "La mejor forma de reservar tu cancha"
- **Target:** Jugadores casuales, ciudades argentinas
- **Tamano:** 5+ anos, cobertura en 15+ paises LATAM
- **Pricing:** No publico
- **Apps:** iOS + Android
- **Fortalezas:** Cobertura multi-ciudad en Argentina, UI mobile-first, Facebook Pixel (hacen ads)
- **Debilidades:** Sin diferenciacion clara vs ATC, sin features sociales, sin contenido, sin sistema para duenos visible
- **Messaging:** Generico. No hay un "por que nosotros" claro

#### Canchas.club
- **Que hace:** Marketplace de canchas deportivas
- **Tagline:** "Encontra y reserva canchas deportivas en Argentina"
- **Target:** Jugadores + duenos de canchas
- **Pricing:** 3% comision por transaccion a los complejos
- **Fortalezas:** Modelo marketplace simple, herramientas para duenos (gestion, promocion)
- **Debilidades:** Comision del 3% (TuCancha cobra 0%), sin app nativa, sin features sociales, pagina inestable
- **Messaging:** Funcional, sin diferenciacion

#### QuieroCancha
- **Que hace:** Software de gestion para clubes deportivos (B2B puro)
- **Tagline:** "Administrar un club deportivo nunca fue tan facil"
- **Target:** Duenos de complejos exclusivamente (no tiene marketplace para jugadores)
- **Pricing:** 1 mes gratis, despues suscripcion mensual
- **Features:** Reservas, control de ingresos/gastos, inventario, WhatsApp/chatbot, app mobile, facturacion
- **Fortalezas:** Feature set completo para gestion (inventario, facturacion, contabilidad), IA mencionada
- **Debilidades:** No tiene lado jugador (no es marketplace), sin presencia social, nicho B2B puro

### Messaging Comparison Matrix

| Dimension | TuCancha | ATC Sports | DondeJuego | Canchas.club |
|---|---|---|---|---|
| **Tagline** | "Reserva tu cancha al instante" | "Tu proximo partido, a un link de distancia" | "La mejor forma de reservar tu cancha" | "Encontra y reserva canchas deportivas" |
| **Target principal** | Jugadores + duenos | Complejos + jugadores | Jugadores | Jugadores + duenos |
| **Diferenciador** | Sin comisiones + Falta Uno + Torneos | #1 en LATAM, 14 paises | Cobertura ciudades | Marketplace simple |
| **Tono** | Argentino, directo, joven | Corporativo, funcional | Neutro, generico | Neutro, funcional |
| **Modelo de negocio** | Suscripcion flat (0% comision) | Suscripcion mensual | No publico | 3% comision |
| **Feature social** | Falta Uno (buscar jugadores) | Tiene "partidos" basico | No | No |
| **Perfil deportivo** | Si (stats, rating, categoria) | No | No | No |
| **Torneos** | En desarrollo | No | No | No |

### Narrative Analysis

| | TuCancha | ATC | DondeJuego |
|---|---|---|---|
| **Villano** | Las llamadas, los grupos de WhatsApp, el "preguntale al encargado" | La falta de un canal online | La dificultad de encontrar cancha |
| **Heroe** | El jugador que quiere jugar ya | El complejo que quiere digitalizarse | El jugador buscando cancha |
| **Transformacion** | De no poder juntar gente a jugar en 2 minutos | De gestion manual a gestion online | De buscar a reservar |
| **Stakes** | Te perdes el partido de tu vida | Perdes clientes y plata | No jugas |

TuCancha tiene la narrativa mas emocional y relatable. La historia del jueves a la noche jugando al truco es poderosa. Los competidores hablan de funcionalidad; TuCancha habla de frustracion real.

### Positioning Map

```
                    SOCIAL (jugadores + comunidad)
                              |
                    TuCancha  |
                      (aqui)  |
                              |
   SIMPLE --------------------+-------------------- ENTERPRISE
                              |
               Canchas.club   |        ATC Sports
                              |        QuieroCancha
                              |
                    TRANSACCIONAL (solo reservar)
```

TuCancha es el unico player en el cuadrante "Social + Simple". Este es el espacio mas valioso porque los jugadores no solo quieren reservar — quieren jugar, encontrar gente, competir.

### Opportunities

1. **"Red social deportiva" positioning** — Ningun competidor conecta jugadores entre si. Falta Uno + Perfil Deportivo + Torneos = TuCancha es donde los deportistas van, no solo donde reservan
2. **$0 comisiones como battlecry** — Canchas.club cobra 3%, ATC cobra mensual. "Sin comisiones sobre tus ingresos" es un mensaje simple y poderoso para duenos
3. **Content SEO first-mover** — Literalmente ningun competidor tiene blog. El primero que publique contenido relevante domina las busquedas informacionales
4. **Torneos como moat** — Nadie tiene sistema de torneos. Cuando se lance, es un diferenciador imposible de copiar rapido
5. **Argentinidad como identidad** — ATC es LATAM generico. TuCancha puede ser "la app argentina" con identidad local fuerte

### Threats

1. **ATC tiene masa critica** — Si un dueno de complejo ya usa ATC, el switching cost es real
2. **Apps nativas** — ATC y DondeJuego tienen apps iOS/Android. TuCancha es web-only (la PWA mitiga parcialmente)
3. **ATC podria copiar Falta Uno** — Tienen un feature de "partidos" basico que podrian expandir
4. **Financiamiento** — ATC probablemente tiene inversion (14 paises no se financian solos). TuCancha compite bootstrapped

### Recommended Actions

**Quick wins (esta semana):**
1. Agregar "Sin comisiones" y "Falta Uno" como diferenciadores en el homepage hero
2. Crear una landing de comparacion `/tucancha-vs-atc`
3. Publicar 3 posts de blog con keywords de baja competencia

**Strategic (este trimestre):**
4. Reposicionar de "plataforma de reservas" a "red social deportiva"
5. Lanzar Torneos ASAP
6. PWA push notifications agresivas

---

## 4. Campaign Plan — Q2 2026

### Campaign Overview

**Nombre:** "Juga sin vueltas"
**Resumen:** Campana de 8 semanas para posicionar TuCancha como la primera red social deportiva de Argentina, captar los primeros 50 complejos y 500 jugadores activos.
**Objetivo principal:** 50 complejos registrados + 500 usuarios activos en 8 semanas.
**Objetivos secundarios:** Establecer presencia SEO con contenido indexable, generar awareness en redes sociales, validar el messaging "sin comisiones" + "Falta Uno".

### Target Audience

**Segmento primario — Jugadores (B2C):**
Hombres 18-40 en Argentina que juegan futbol, padel o tenis al menos 1 vez por semana. Su dolor: coordinar por WhatsApp es un caos, llamar complejos es un embole, y siempre falta uno para completar el equipo. Estan en Instagram, TikTok, grupos de WhatsApp deportivos, y buscan en Google "canchas cerca" cuando necesitan reservar.

**Segmento secundario — Duenos de complejos (B2B):**
Duenos o encargados de complejos deportivos con 2-10 canchas. Su dolor: canchas vacias en horarios muertos, cobros manuales, faltas sin aviso, y cero visibilidad online. Estan en Facebook, Google (buscan "software gestion complejo deportivo"), y en grupos de WhatsApp de duenos de complejos.

### Key Messages

**Mensaje core:** "Reserva, juga, y encontra jugadores. Todo en un lugar, sin comisiones."

| Mensaje | Para quien | Proof point |
|---|---|---|
| "Te falta uno? TuCancha te conecta con jugadores cerca tuyo" | Jugadores | Feature Falta Uno |
| "$0 comisiones. Tu plata es tu plata" | Duenos | Modelo suscripcion flat vs 3% de Canchas.club |
| "Reserva en 30 segundos, sin llamar a nadie" | Jugadores | Flujo de reserva simple, pago con MercadoPago |
| "Tus canchas se llenan solas. Vos enfocate en el negocio" | Duenos | Reservas online 24/7 + cobro automatico + panel |

### Channel Strategy

| Canal | Por que | Formato | Esfuerzo | Budget |
|---|---|---|---|---|
| **Instagram** | Audiencia joven deportiva, visual | Reels, stories, posts | Medio | Organico + $50-100/sem en ads |
| **TikTok** | Viralidad, publico joven, nadie lo hace en el nicho | Reels cortos (15-30s) | Medio | Organico |
| **Blog SEO** | Nadie en la competencia lo tiene, captura long-tail | Articulos 800-1500 palabras | Medio | $0 (content propio) |
| **WhatsApp** | Canal donde viven los jugadores argentinos | Mensajes directos, grupos | Bajo | $0 |
| **Google My Business** | Presencia local, reviews | Perfil de empresa | Bajo | $0 |
| **Google Ads** (semana 5+) | Capturar intent transaccional | Search ads | Bajo | $30-50/sem |

### Content Calendar

| Semana | Contenido | Canal | Notas |
|---|---|---|---|
| **S1** (14-20 abr) | Post lanzamiento "Ya estamos online" + video demo 30s | IG, TikTok | Crear cuenta IG + TikTok |
| **S1** | Blog: "Cuanto cuesta alquilar una cancha de futbol 5 en 2026" | Blog | SEO keyword baja competencia |
| **S1** | Crear Google My Business | Google | Perfil + fotos |
| **S2** (21-27 abr) | Reel: "Asi de facil es reservar en TuCancha" (screen recording) | IG, TikTok | 15s, musica trending |
| **S2** | Blog: "Como organizar un partido de futbol con amigos sin volverte loco" | Blog | SEO + link a Falta Uno |
| **S2** | Story poll: "Cuanto tardas en reservar una cancha?" | IG Stories | Engagement |
| **S3** (28 abr-4 may) | Reel: "Falta Uno: encontra jugadores cerca tuyo" (demo feature) | IG, TikTok | Feature hero, 20s |
| **S3** | Blog: "Guia de categorias de padel en Argentina" | Blog | SEO + link a perfil deportivo |
| **S3** | Contacto directo a 20 complejos via WhatsApp/Instagram | WhatsApp, IG DM | Pitch personal, ofrecer 30 dias gratis |
| **S4** (5-11 may) | Reel: "$0 comisiones. En serio." (comparativa visual) | IG, TikTok | Mensaje battlecard vs competencia |
| **S4** | Blog: "Software de gestion para complejos deportivos: que buscar" | Blog | B2B SEO, link a /planes |
| **S4** | FAQ page con schema markup | Web | SEO People Also Ask |
| **S5** (12-18 may) | Reel: testimonio primer complejo real | IG, TikTok | Social proof |
| **S5** | Activar Google Ads — "reservar cancha online" + "cancha de padel" | Google Ads | $30-50/sem, landing optimizada |
| **S5** | Blog: "Las mejores canchas de futbol 5 en [tu ciudad]" | Blog | Geo SEO |
| **S6** (19-25 may) | Reel: "Tu perfil deportivo en TuCancha" (stats, rating, historial) | IG, TikTok | Feature showcase |
| **S6** | Email a complejos registrados: tips para llenar horarios muertos | Email | Retencion B2B |
| **S6** | Blog: "Diferencias entre cancha de futbol 5, 7 y 11" | Blog | SEO informacional |
| **S7** (26 may-1 jun) | Reel: "Hicimos lo que nadie hizo: torneos online" (teaser) | IG, TikTok | Genera expectativa |
| **S7** | Contacto segunda ronda: 20 complejos mas | WhatsApp, IG DM | Con datos de los primeros resultados |
| **S8** (2-8 jun) | Post resumen: "Primer mes de TuCancha en numeros" | IG, Blog | Transparencia, construye trust |
| **S8** | Analisis de metricas, ajuste de estrategia | Interno | Review y pivoteo |

### Success Metrics

| KPI | Target | Como medir | Cadencia |
|---|---|---|---|
| Complejos registrados | 50 | Dashboard admin TuCancha | Semanal |
| Usuarios registrados | 500 | Dashboard admin | Semanal |
| Reservas completadas | 200 | BD + MercadoPago | Semanal |
| Partidos Falta Uno creados | 30 | BD | Semanal |
| Trafico organico blog | 1000 visitas/mes | Google Analytics | Mensual |
| Seguidores Instagram | 500 | IG analytics | Semanal |
| Posicion Google "reservar cancha online" | Top 30 | Google Search Console | Mensual |
| Costo por complejo adquirido (Google Ads) | < $500 ARS | Google Ads | Semanal |

### Budget Allocation

| Categoria | Mensual | 8 semanas |
|---|---|---|
| Instagram Ads | $15.000-25.000 ARS | $30.000-50.000 ARS |
| Google Ads | $12.000-20.000 ARS | $24.000-40.000 ARS |
| Herramientas (Canva Pro, analytics) | $5.000 ARS | $10.000 ARS |
| **Total estimado** | **$32.000-50.000 ARS** | **$64.000-100.000 ARS** |

### Risks and Mitigations

| Riesgo | Mitigacion |
|---|---|
| No consigo complejos (chicken-and-egg) | Ofrecer 60 dias gratis, onboarding personalizado, cargar datos iniciales por ellos |
| Contenido no rankea rapido | Long-tail keywords de baja competencia primero, complementar con ads |
| Instagram no genera traccion | TikTok como canal secundario, WhatsApp directo como canal primario para B2B |

---

## 5. Draft Content

*Brand voice: argentino, directo, joven. Voseo. Sin corporativismo. Energia deportiva contenida.*

### Blog Post 1: "Cuanto cuesta alquilar una cancha de futbol 5 en Argentina (2026)"

**Primary keyword:** "cuanto cuesta alquilar cancha futbol 5"
**Meta description:** Precios actualizados de canchas de futbol 5 en Argentina. Cuanto sale alquilar por hora, que incluye, y como reservar online sin llamar.

---

Alquilar una cancha de futbol 5 en Argentina puede costar desde $8.000 hasta $40.000 por hora dependiendo de la zona, el horario y el tipo de cancha. Si alguna vez intentaste averiguar precios llamando a complejos y nadie te atendio, esta guia es para vos.

**Precios promedio por zona**

Los precios varian mucho segun donde juegues. En AMBA (Buenos Aires y alrededores), una cancha de futbol 5 de pasto sintetico cuesta entre $15.000 y $35.000 la hora en horario nocturno (18 a 22hs). En el interior del pais, los precios bajan: entre $8.000 y $20.000 la hora.

Los horarios de la manana y la siesta son mas baratos. Si podes jugar antes de las 18hs, vas a pagar entre un 20% y 40% menos que en horario pico.

**Que incluye el precio**

La mayoria de los complejos incluyen la cancha, iluminacion, vestuarios y estacionamiento. Algunos cobran aparte las pecheras, la pelota o el uso de parrilla post-partido. Siempre conviene preguntar antes de reservar.

**Como reservar sin llamar**

La forma mas simple es usar una plataforma de reservas online. En TuCancha podes buscar complejos cerca tuyo, ver los horarios disponibles y reservar en menos de un minuto. Pagas online con MercadoPago y listo: llegas y jugas.

La ventaja vs llamar: ves la disponibilidad real en el momento, sin esperar que alguien te conteste. Y si te falta un jugador, podes publicar el partido en Falta Uno para que se sumen jugadores de la zona.

**Tips para ahorrar**

- Juga en horarios de baja demanda (manana, siesta, lunes a miercoles)
- Reserva con anticipacion: muchos complejos tienen descuento por reserva anticipada
- Arma un grupo fijo y reserva siempre el mismo horario: algunos complejos dan precio especial por recurrencia
- Usa plataformas sin comisiones como TuCancha, donde el precio que ves es el precio que pagas

**CTA:** Busca canchas de futbol 5 cerca tuyo y reserva al instante en TuCancha.

---

### Blog Post 2: "Como organizar un partido de futbol con amigos sin volverte loco"

**Primary keyword:** "como organizar partido de futbol"
**Meta description:** Guia para armar un partido de futbol con amigos: como juntar gente, reservar cancha, dividir el pago y que hacer cuando falta uno.

---

Todos conocemos la historia. El grupo de WhatsApp explota un martes con "dale, el jueves jugamos?". Empiezan los "yo puedo", los "no se, te confirmo", los audios de 2 minutos, y el jueves a las 19hs todavia no sabes ni cuantos son ni donde jugar.

Organizar un partido de futbol con amigos no deberia ser un trabajo de tiempo completo. Aca va la guia para hacerlo sin perder la cordura.

**Paso 1: Defini dia y horario antes de preguntar**

El error mas comun es preguntar "cuando pueden?". Nunca van a ponerse de acuerdo. Elegi un dia y horario fijo y pregunta "quien viene el jueves a las 21?". Si o no. Sin vueltas.

**Paso 2: Reserva la cancha antes de confirmar todos**

No esperes a tener 10 confirmados para reservar. Las canchas en horario pico se llenan rapido. Reserva apenas tengas 6-7 confirmados y el resto se suma. En TuCancha podes reservar online en 30 segundos y cancelar si no juntas la gente.

**Paso 3: Resolve el problema de "falta uno"**

Siempre falta uno. Siempre. En vez de mandar 40 mensajes individuales preguntando "conoces a alguien?", usa Falta Uno de TuCancha. Publicas tu partido con la cancha, horario y cuantos faltan. Jugadores de la zona se suman solos. Sin coordinar, sin rogar.

**Paso 4: Dividi el pago sin drama**

"Yo pago y despues me pasan". No. Con pago online cada uno paga su parte antes del partido. Asi no corres detras de nadie y el que no pago no juega. En TuCancha el cobro es automatico con MercadoPago.

**Paso 5: Llega y juga**

Sin llamar al complejo, sin pasar por la oficina, sin preguntar "en que cancha era?". Reservaste, pagaste, llegas, jugas. Asi de simple deberia ser siempre.

**CTA:** Organiza tu proximo partido en TuCancha. Reserva la cancha, invita a tus amigos, y si falta uno, la app se encarga.

---

### Blog Post 3: "Guia de categorias de padel en Argentina: de octava a primera"

**Primary keyword:** "categorias padel argentina"
**Meta description:** Todo sobre las categorias de padel en Argentina: de octava a primera. Como saber cual es tu nivel, que se espera en cada categoria, y como subir.

---

Si recien estas empezando en el padel o nunca jugaste un torneo, las categorias pueden parecer confusas. Octava, septima, quinta... que significan y como saber donde estas? Aca te lo explicamos simple.

**Como funciona el sistema de categorias**

En Argentina el padel se organiza en 8 categorias, de octava (principiante) a primera (profesional/semi-profesional). La mayoria de los jugadores recreativos estan entre octava y quinta.

**Octava categoria** — Recien empezando. Estas aprendiendo a pegar de reves, la bandeja te sale 1 de cada 5 veces, y todavia no controlas bien la pared. No pasa nada, todos empezamos aca.

**Septima categoria** — Ya manejas los golpes basicos. Podes mantener un peloteo, sabes cuando ir a la red y cuando quedarte atras. Las paredes todavia te complican pero cada vez menos.

**Sexta categoria** — Jugas seguido y se nota. Tenes un juego armado, podes hacer un par de golpes ganadores por set, y empezas a pensar tacticamente.

**Quinta categoria** — Intermedio solido. Buena tecnica en la mayoria de los golpes, sabes leer el juego del rival, y podes competir en torneos locales.

**Cuarta categoria** — Intermedio-alto. Tenes golpes definidos, buena lectura de pared, y consistencia. Competis regularmente y ganas partidos.

**Tercera categoria** — Competitivo alto. Jugas torneos seguido, tu tecnica es solida y tenes variedad de recursos. La diferencia con cuarta es la consistencia y la presion bajo puntos importantes.

**Segunda categoria** — Muy competitivo. Nivel de circuito provincial. Todos los golpes pulidos, excelente fisico, y mentalidad de torneo.

**Primera categoria** — El nivel mas alto. Circuito nacional, jugadores que dedican tiempo serio al padel. Tecnica impecable y estrategia avanzada.

**Como saber tu categoria**

Lo mas honesto es autoevaluarte segun la descripcion de arriba y despues validarlo jugando. En TuCancha, cuando creas tu perfil deportivo de padel, elegis tu categoria. Despues de 5 partidos jugados, la plataforma ajusta tu categoria automaticamente basandose en las evaluaciones de otros jugadores. Asi se mantiene justo para todos.

**Por que importa elegir bien**

Si te pones en una categoria mas alta de la que sos, vas a perder siempre y no la vas a pasar bien. Si te pones mas baja, vas a ganar siempre y los rivales no la pasan bien. Elegi honestamente y los partidos van a ser mas parejos y mas divertidos para todos.

**CTA:** Crea tu perfil deportivo de padel en TuCancha y empeza a jugar partidos de tu nivel.

---

### Instagram Posts (5 posts iniciales)

**Post 1 — Lanzamiento**
Imagen: Screenshot de la app con fondo verde
Copy: "TuCancha ya esta online. Reserva canchas de futbol, padel y tenis en segundos. Sin llamar. Sin WhatsApp. Sin complicaciones. Link en bio."
Hashtags: #TuCancha #ReservasOnline #FutbolArgentina #Padel #CanchasDeFutbol

**Post 2 — Feature Falta Uno**
Imagen: Mockup de la pantalla Falta Uno
Copy: "Te falta uno para completar el equipo? Publica tu partido en Falta Uno y jugadores de tu zona se suman solos. Sin mandar 40 mensajes. Sin rogar."
Hashtags: #FaltaUno #FutbolConAmigos #TuCancha #JugaHoy #Futbol5

**Post 3 — Sin comisiones**
Imagen: Comparativa visual "Otros: 3% comision / TuCancha: $0"
Copy: "Sos dueno de un complejo? En TuCancha no te cobramos comision sobre tus reservas. Precio fijo, sin sorpresas, sin letra chica. Tu plata es tu plata."
Hashtags: #ComplejosDeportivos #GestionDeportiva #SinComisiones #TuCancha

**Post 4 — Perfil deportivo**
Imagen: Screenshot del perfil deportivo publico
Copy: "Tu perfil deportivo: partidos jugados, victorias, rating, categoria. Todo en un solo lugar. Construi tu reputacion como jugador."
Hashtags: #PerfilDeportivo #TuCancha #PadelArgentina #FutbolAmateur

**Post 5 — Engagement**
Imagen: Fondo cancha verde con texto overlaid
Copy: "Jueves a la noche. El grupo de WhatsApp dice 'yo puedo' pero nadie reserva. Suena familiar? Hay otra forma. TuCancha."
Hashtags: #JuevesDeCancha #Futbol5 #TuCancha #ReservaOnline

---

### Pitch WhatsApp para Complejos

**Version corta (primer contacto):**

> Hola! Soy Santiago de TuCancha, una plataforma de reservas online para complejos deportivos. Los jugadores reservan y pagan online, vos recibis la plata directo en tu MercadoPago. Sin comisiones. Te puedo mostrar como funciona en 5 minutos?

**Version larga (si responde con interes):**

> Genial! TuCancha funciona asi: creas tu perfil de complejo, cargas tus canchas con horarios y precios, y listo. Los jugadores te encuentran en la plataforma, ven disponibilidad en tiempo real y reservan pagando online.
>
> Lo que nos diferencia:
> - $0 de comision sobre tus reservas (cobramos una suscripcion mensual fija)
> - Los jugadores pagan con MercadoPago, la plata va directo a tu cuenta
> - Panel de gestion con agenda, reportes y historial
> - Feature "Falta Uno": los jugadores publican partidos y se llenan con gente de la zona (mas gente jugando = mas reservas para vos)
>
> Te hacemos el setup gratis y tenes 30 dias de prueba sin compromiso. Queres que lo activemos?

---

### Email Newsletter — Primer mes

**Subject lines:**
1. Tu primer mes en TuCancha: que podes hacer
2. Ya tenes cuenta. Ahora a jugar
3. 3 cosas que podes hacer en TuCancha (ademas de reservar)

**Preview text:** Reservar es solo el principio. Conoce Falta Uno, tu perfil deportivo y mas.

**Body:**

Hola {nombre},

Ya tenes tu cuenta en TuCancha. Ahora te contamos 3 cosas que podes hacer:

**1. Reserva una cancha al instante**
Busca complejos cerca tuyo, elegi el horario y paga online. Sin llamar, sin esperar.

**2. Publica un partido en Falta Uno**
Te faltan jugadores? Publica tu partido y se suman jugadores de tu zona automaticamente.

**3. Crea tu perfil deportivo**
Elegi tu deporte y categoria. A medida que juegues, tu perfil se llena con stats, rating y reputacion.

Si tenes dudas o sugerencias, responde este mail directo. Lo leemos todo.

Nos vemos en la cancha,
El equipo de TuCancha
