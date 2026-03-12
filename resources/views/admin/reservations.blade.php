<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Reservas de mis canchas</title>
</head>
<body>

<h1>Reservas de mis canchas</h1>

<table border="1" cellpadding="6">
<tr>
    <th>ID</th>
    <th>Complejo</th>
    <th>Cancha</th>
    <th>Usuario</th>
    <th>Horario</th>
    <th>Estado</th>
</tr>

@forelse($reservations as $r)
<tr>
    <td>{{ $r->id }}</td>
    <td>{{ $r->field->venue->name }}</td>
    <td>{{ $r->field->name }}</td>
    <td>{{ $r->user->name }}</td>
    <td>{{ $r->start_at->format('d/m/Y H:i') }}</td>
    <td>{{ $r->status }}</td>
</tr>
@empty
<tr>
<td colspan="6">No hay reservas</td>
</tr>
@endforelse

</table>

</body>
</html>