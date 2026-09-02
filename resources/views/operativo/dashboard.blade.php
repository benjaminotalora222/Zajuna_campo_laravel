<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Operativo — Zajuna Campo</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center" style="background:#fdf9ee;">
    <div class="text-center">
        <p class="text-xs font-bold uppercase tracking-widest mb-3" style="color:#71277a;">Operativo</p>
        <h1 class="text-2xl font-bold mb-2" style="color:#1f3410;">Panel Operativo</h1>
        <p style="color:#5a5a4f;">Próximamente disponible.</p>
        <form method="POST" action="{{ route('logout') }}" class="mt-6">
            @csrf
            <button type="submit" class="text-sm font-bold text-white px-5 py-2 rounded-full" style="background:#39a900;">
                Cerrar sesión
            </button>
        </form>
    </div>
</body>
</html>
