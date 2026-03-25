<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>{{ config('app.name', 'TuCancha') }}</title>
<link rel="icon" type="image/svg+xml" href="/images/favicon.svg">

<style>
* { box-sizing:border-box; }

body{
margin:0;
font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;
background:#f7f7f8;
color:#111;
display:flex;
flex-direction:column;
min-height:100vh;
}

/* HEADER */

.auth-header{
background:#fff;
border-bottom:1px solid #eee;
padding:16px 32px;
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
height:56px;
width:auto;
display:block;
}
.brand-icon{
height:56px;
width:56px;
display:none;
}

@media (max-width:639px){
  .brand-full{display:none;}
  .brand-icon{display:block;}
}

/* MAIN */

.auth-main{
flex:1;
display:flex;
align-items:center;
justify-content:center;
padding:40px 20px;
}

/* CARD */

.auth-card{
width:100%;
max-width:420px;
background:#fff;
border-radius:20px;
padding:28px;
border:1px solid #eee;
box-shadow:0 10px 30px rgba(0,0,0,.05);
}

/* INPUTS */

input{
width:100%;
padding:10px 12px;
border-radius:12px;
border:1px solid #ddd;
margin-top:6px;
}

input:focus{
outline:none;
border-color:#111;
}

/* BUTTON */

button{
background:#111;
color:#fff;
border:none;
padding:12px 16px;
border-radius:12px;
font-weight:600;
cursor:pointer;
}

button:hover{
opacity:.9;
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

label{
font-size:14px;
font-weight:600;
}

small{
color:#666;
}

</style>

</head>

<body>

<header class="auth-header">
<a href="{{ route('home') }}" class="brand">
  <img src="/images/logo-multicolor.svg" alt="TuCancha" class="brand-full">
  <img src="/images/logo-multicolor-responsive.svg" alt="TuCancha" class="brand-icon">
</a>

<div>
<a href="{{ route('venues.index') }}">Ver complejos</a>
</div>
</header>

<main class="auth-main">

<div class="auth-card">
{{ $slot }}
</div>

</main>

</body>
</html>
