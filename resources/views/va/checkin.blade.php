@extends('layouts.app')

@section('title','Check-in')

@section('content')

<div class="page-card" style="max-width:600px;margin:auto;">

  <h1 style="margin-top:0;">Check-in de reserva</h1>

  <p class="muted">
    Pedile al cliente su código de reserva.
  </p>

  <form method="POST" action="{{ route('va.checkin.store') }}" style="margin-top:20px;">
    @csrf

    <input
      name="code"
      placeholder="Código de verificación"
      required
      style="
        width:100%;
        padding:16px;
        font-size:20px;
        border:1px solid #ddd;
        border-radius:12px;
        letter-spacing:4px;
        text-align:center;
      "
    >

    <button
      type="submit"
      class="btn btn-primary"
      style="margin-top:16px;width:100%;padding:14px;font-size:18px;"
    >
      Validar check-in
    </button>

  </form>

</div>

@endsection