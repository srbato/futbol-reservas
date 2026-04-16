<!DOCTYPE html>
<html lang="es-AR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="description" content="Ingresa a TuCancha — Reserva canchas de futbol, padel y tenis online en Argentina.">
<meta name="robots" content="noindex, nofollow">
<link rel="alternate" hreflang="es-AR" href="{{ url()->current() }}">

<meta property="og:type" content="website">
<meta property="og:site_name" content="TuCancha">
<meta property="og:title" content="TuCancha — Ingresa a tu cuenta">
<meta property="og:description" content="Reserva canchas de futbol, padel y tenis online en Argentina.">
<meta property="og:image" content="{{ asset('images/og-default.png') }}">

<title>{{ config('app.name', 'TuCancha') }}</title>
<link rel="icon" type="image/svg+xml" href="/images/favicon.svg">
<link rel="apple-touch-icon" sizes="180x180" href="/images/apple-touch-icon.png">
<link rel="manifest" href="/site.webmanifest">
<link rel="canonical" href="{{ url()->current() }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/css/design-tokens.css">

<style>
* { box-sizing:border-box; margin:0; padding:0; }

body{
font-family:'Sora',system-ui,-apple-system,sans-serif;
background:#050505;
color:#e8e8e8;
min-height:100vh;
overflow-x:hidden;
-webkit-font-smoothing:antialiased;
}

/* ===================== TWO-COLUMN LAYOUT ===================== */

.auth-wrapper{
display:flex;
min-height:100vh;
}

/* --- LEFT: Hero image panel (desktop only) --- */

.auth-hero{
display:none;
position:relative;
width:50%;
overflow:hidden;
background:#0a0a0a;
}

.auth-hero-img{
position:absolute;
inset:0;
width:100%;
height:100%;
object-fit:cover;
opacity:.5;
animation:heroZoom 25s ease-in-out infinite alternate;
}

@keyframes heroZoom{
0%  {transform:scale(1);}
100%{transform:scale(1.08);}
}

.auth-hero-overlay{
position:absolute;
inset:0;
background:linear-gradient(135deg,rgba(0,0,0,.6) 0%,rgba(34,197,94,.15) 100%);
display:flex;
flex-direction:column;
justify-content:flex-end;
padding:48px;
}

.auth-hero-overlay h2{
color:#fff;
font-size:32px;
font-weight:800;
line-height:1.2;
margin-bottom:12px;
letter-spacing:-0.02em;
}

.auth-hero-overlay p{
color:rgba(255,255,255,.7);
font-size:16px;
line-height:1.5;
max-width:380px;
}

/* --- RIGHT: Form panel --- */

.auth-panel{
width:100%;
min-height:100vh;
display:flex;
flex-direction:column;
position:relative;
}

/* Mobile background */
.auth-panel-bg{
display:block;
position:absolute;
inset:0;
z-index:0;
overflow:hidden;
}

.auth-panel-bg img{
width:100%;
height:100%;
object-fit:cover;
opacity:.06;
filter:blur(2px);
}

/* HEADER */

.auth-header{
position:relative;
z-index:2;
background:rgba(10,10,10,.85);
backdrop-filter:blur(12px);
-webkit-backdrop-filter:blur(12px);
border-bottom:1px solid rgba(255,255,255,.06);
padding:14px 24px;
display:flex;
justify-content:space-between;
align-items:center;
}

.brand{
display:flex;
align-items:center;
text-decoration:none;
}
.brand-full{
height:48px;
width:auto;
display:block;
}
.brand-icon{
height:48px;
width:48px;
display:none;
}

.auth-header-link{
font-size:14px;
font-weight:500;
color:#e8e8e8;
text-decoration:none;
padding:8px 16px;
border-radius:10px;
border:1px solid rgba(255,255,255,.12);
transition:all .2s ease;
}

.auth-header-link:hover{
background:#22c55e;
color:#050505;
border-color:#22c55e;
text-decoration:none;
}

/* MAIN */

.auth-main{
position:relative;
z-index:2;
flex:1;
display:flex;
align-items:center;
justify-content:center;
padding:32px 20px;
}

/* CARD */

.auth-card{
width:100%;
max-width:440px;
background:rgba(17,17,17,.92);
backdrop-filter:blur(16px);
-webkit-backdrop-filter:blur(16px);
border-radius:24px;
padding:32px 28px;
border:1px solid rgba(255,255,255,.08);
box-shadow:0 20px 60px rgba(0,0,0,.4), 0 1px 3px rgba(0,0,0,.2);
animation:cardSlideUp .6s cubic-bezier(.16,1,.3,1) both;
}

@keyframes cardSlideUp{
0%  {opacity:0; transform:translateY(24px);}
100%{opacity:1; transform:translateY(0);}
}

/* FORM ELEMENTS */

label{
font-size:14px;
font-weight:600;
color:#a0a0a0;
}

input[type="text"],
input[type="email"],
input[type="password"]{
width:100%;
padding:11px 14px;
border-radius:12px;
border:1.5px solid rgba(255,255,255,.1);
margin-top:6px;
font-size:15px;
background:#0a0a0a;
color:#e8e8e8;
font-family:'Sora',sans-serif;
transition:border-color .2s ease, box-shadow .2s ease;
}

input[type="text"]:focus,
input[type="email"]:focus,
input[type="password"]:focus{
outline:none;
border-color:#22c55e;
box-shadow:0 0 0 3px rgba(34,197,94,.12);
}

input[type="checkbox"]{
width:auto;
accent-color:#22c55e;
}

/* BUTTON (green CTA) */

.auth-btn{
background:#22c55e;
color:#050505;
border:none;
padding:12px 24px;
border-radius:12px;
font-weight:700;
font-size:15px;
cursor:pointer;
transition:all .2s ease;
width:100%;
font-family:'Sora',sans-serif;
}

.auth-btn:hover{
background:#16a34a;
transform:translateY(-1px);
box-shadow:0 4px 12px rgba(34,197,94,.3);
}

.auth-btn:active{
transform:translateY(0);
}

/* LINKS */

a{
color:#e8e8e8;
text-decoration:none;
}

a:hover{
text-decoration:underline;
}

/* TEXT */

small{
color:#666;
}

/* ===================== DESKTOP ===================== */

@media (min-width:1024px){
  .auth-hero{
    display:block;
  }

  .auth-panel{
    width:50%;
  }

  .auth-panel-bg{
    display:none;
  }

  .auth-header{
    background:#0a0a0a;
    backdrop-filter:none;
    border-bottom:1px solid rgba(255,255,255,.06);
  }

  .auth-card{
    background:#111;
    backdrop-filter:none;
    max-width:460px;
    padding:36px 32px;
    border:1px solid rgba(255,255,255,.08);
  }

  .brand-full{ height:52px; }
  .brand-icon{ height:52px; width:52px; }
}

@media (max-width:639px){
  .brand-full{display:none;}
  .brand-icon{display:block;}
}

</style>

</head>

<body>

<div class="auth-wrapper">

<!-- LEFT: Hero image (desktop) -->
<div class="auth-hero">
  <img src="/images/hero-cancha.webp" alt="Cancha de futbol" class="auth-hero-img" aria-hidden="true">
  <div class="auth-hero-overlay">
    <h2>Tu cancha favorita,<br>a un click.</h2>
    <p>Reserva, paga y juga. Sin llamadas, sin esperas.</p>
  </div>
</div>

<!-- RIGHT: Form panel -->
<div class="auth-panel">

  <!-- Mobile subtle background -->
  <div class="auth-panel-bg">
    <img src="/images/hero-cancha.webp" alt="" aria-hidden="true">
  </div>

  <header class="auth-header">
    <a href="{{ route('home') }}" class="brand">
      <img src="/images/logo-fondonegro-multicolor.svg" alt="TuCancha" class="brand-full">
      <img src="/images/logo-fondonegro-multicolor-responsive.svg" alt="TuCancha" class="brand-icon">
    </a>

    <a href="{{ route('venues.index') }}" class="auth-header-link">Ver complejos</a>
  </header>

  <main class="auth-main">
    <div class="auth-card">
      {{ $slot }}
    </div>
  </main>

</div>

</div>

</body>
</html>
