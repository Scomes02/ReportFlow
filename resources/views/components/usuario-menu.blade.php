{{-- Placeholder simple; el logout completo con su ruta lo define la dupla de Auth --}}
<span class="text-xs text-white/80">
    {{ auth()->user()->name ?? 'Usuario' }}
</span>