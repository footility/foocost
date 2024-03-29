<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cost Estimation</title>
  <!-- Aggiungi Bootstrap tramite CDN -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
  <h1 class="mb-4">Foocost</h1>
  <table class="table table-bordered">
    <thead class="thead-dark">
    <tr>
      <th>Table Name</th>
      <th>Number of Fields</th>
      <th>Cost (€)</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($costsDetails as $detail)
      <tr>
        <td>{{ $detail['table'] }}</td>
        <td>{{ $detail['fields'] }}</td>
        <td>€{{ number_format($detail['cost'], 2) }}</td>
      </tr>
    @endforeach
    <tr class="table-primary">
      <td colspan="2"><strong>Total Cost</strong></td>
      <td><strong>€{{ number_format($totalCost, 2) }}</strong></td>
    </tr>
    </tbody>
  </table>
</div>
<!-- Opzionale: Aggiungi JavaScript di Bootstrap -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
