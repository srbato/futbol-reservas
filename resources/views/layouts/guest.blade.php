<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>{{ config('app.name', 'TuCancha') }}</title>
<link rel="icon" type="image/svg+xml" href="/images/favicon.svg">
<link rel="canonical" href="{{ url()->current() }}">

<style>
* { box-sizing:border-box; margin:0; padding:0; }

body{
font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;
background:#f7f7f8;
color:#111;
min-height:100vh;
overflow-x:hidden;
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
opacity:.7;
animation:heroZoom 25s ease-in-out infinite alternate;
}

@keyframes heroZoom{
0%  {transform:scale(1);}
100%{transform:scale(1.08);}
}

.auth-hero-overlay{
position:absolute;
inset:0;
background:linear-gradient(135deg,rgba(0,0,0,.55) 0%,rgba(22,163,74,.25) 100%);
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
}

.auth-hero-overlay p{
color:rgba(255,255,255,.8);
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
opacity:.12;
filter:blur(2px);
}

/* HEADER */

.auth-header{
position:relative;
z-index:2;
background:rgba(255,255,255,.85);
backdrop-filter:blur(12px);
-webkit-backdrop-filter:blur(12px);
border-bottom:1px solid rgba(0,0,0,.06);
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
color:#111;
text-decoration:none;
padding:8px 16px;
border-radius:10px;
border:1px solid #e5e7eb;
transition:all .2s ease;
}

.auth-header-link:hover{
background:#16a34a;
color:#fff;
border-color:#16a34a;
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
background:rgba(255,255,255,.92);
backdrop-filter:blur(16px);
-webkit-backdrop-filter:blur(16px);
border-radius:24px;
padding:32px 28px;
border:1px solid rgba(0,0,0,.06);
box-shadow:0 20px 60px rgba(0,0,0,.08), 0 1px 3px rgba(0,0,0,.04);
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
color:#374151;
}

input[type="text"],
input[type="email"],
input[type="password"]{
width:100%;
padding:11px 14px;
border-radius:12px;
border:1.5px solid #e5e7eb;
margin-top:6px;
font-size:15px;
background:#fff;
color:#111;
transition:border-color .2s ease, box-shadow .2s ease;
}

input[type="text"]:focus,
input[type="email"]:focus,
input[type="password"]:focus{
outline:none;
border-color:#16a34a;
box-shadow:0 0 0 3px rgba(22,163,74,.12);
}

input[type="checkbox"]{
width:auto;
accent-color:#16a34a;
}

/* BUTTON (green CTA) */

.auth-btn{
background:#16a34a;
color:#fff;
border:none;
padding:12px 24px;
border-radius:12px;
font-weight:600;
font-size:15px;
cursor:pointer;
transition:all .2s ease;
width:100%;
}

.auth-btn:hover{
background:#15803d;
transform:translateY(-1px);
box-shadow:0 4px 12px rgba(22,163,74,.3);
}

.auth-btn:active{
transform:translateY(0);
}

/* LINKS */

a{
color:#111;
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
    background:#fff;
    backdrop-filter:none;
  }

  .auth-card{
    background:#fff;
    backdrop-filter:none;
    max-width:460px;
    padding:36px 32px;
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
  <img src="/images/hero-cancha.webp" alt="Cancha de futbol" class="auth-hero-img">
  <div class="auth-hero-overlay">
    <h2>Tu cancha favorita,<br>a un click.</h2>
    <p>Reserva, paga y juga. Sin llamadas, sin esperas.</p>
  </div>
</div>

<!-- RIGHT: Form panel -->
<div class="auth-panel">

  <!-- Mobile subtle background -->
  <div class="auth-panel-bg">
    <img src="/images/hero-cancha.webp" alt="">
  </div>

  <header class="auth-header">
    <a href="{{ route('home') }}" class="brand">
      <img src="/images/logo-multicolor.svg" alt="TuCancha" class="brand-full">
      <img src="/images/logo-multicolor-responsive.svg" alt="TuCancha" class="brand-icon">
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
