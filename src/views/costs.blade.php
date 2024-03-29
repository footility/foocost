{{-- resources/views/vendor/foocost/costs.blade.php --}}
        <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Estimated Cost</title>
</head>
<body>
<h1>Estimated Cost</h1>
{{-- Assume $costs contains your calculated data --}}
@foreach ($costs as $cost)
    <p>{{ $cost }}</p>
@endforeach
</body>
</html>
