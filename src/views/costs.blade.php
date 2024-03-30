<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cost Estimation</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
  <h1 class="mb-4">Cost Estimation for Entities</h1>
  <table class="table table-bordered">
    <thead class="thead-dark">
    <tr>
      <th>Entity Name</th>
      @foreach ($uniqueKeys as $key)
        <th>{{ ucfirst($key) }}</th> <!-- Capitalizza la chiave per il titolo della colonna -->
      @endforeach
      <th>Total Time (Minutes)</th>
      <th>Total Cost (€)</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($costsDetails as $detail)
      <tr>
        <td>{{ $detail['entity'] }}</td>
        @foreach ($uniqueKeys as $key)
          <td>{{ $detail[$key] ?? 0 }}</td> <!-- Mostra il valore corrispondente o 0 se non definito -->
        @endforeach
        <td>{{ number_format($detail['time'], 2) }}</td>
        <td>€ {{ number_format($detail['cost'], 2) }}</td>
      </tr>
    @endforeach
    <tr class="table-primary">
      <td colspan="{{ count($uniqueKeys) + 1 }}"><strong>Total Cost</strong></td>
      <td><strong>Days: {{ number_format($totalTime, 2) }}</strong></td>
      <td><strong>€ {{ number_format($totalCost, 2) }}</strong></td>
    </tr>
    </tbody>
  </table>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
