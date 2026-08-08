<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/Solo-logo-hu-uso-diario.png') }}">
    <title>@yield('titulo', 'ReportFlow') · Hospital Universitario</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-gray-50 font-sans text-brandTexto">
    <div class="h-full w-full flex flex-col md:flex-row">
        @include('components.sidebar')

        <main class="flex-1 flex flex-col overflow-y-auto">
            <header class="bg-brandPrimario text-white h-16 flex items-center justify-between px-6 shrink-0 shadow-md">
                <h1 class="text-lg font-black tracking-tight">@yield('titulo', 'ReportFlow')</h1>
                @include('components.usuario-menu')
            </header>

            @if (session('status'))
                <div class="m-6 mb-0 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-lg px-4 py-3">
                    {{ session('status') }}
                </div>
            @endif

            <div class="p-6">
                @yield('contenido')
            </div>
        </main>
    </div>
</body>
</html>