<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Vue with Laravel</title>
    @vite('resources/ts/app.tsx') <!-- Vite with TypeScript -->
</head>
<body>
<div id="app"></div>

<!-- Vite will automatically include the JS -->
</body>
</html>
