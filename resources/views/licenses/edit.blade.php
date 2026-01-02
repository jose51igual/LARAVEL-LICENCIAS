<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Licencia') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('licenses.update', $license) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <label for="name" class="block text-gray-700 text-sm font-bold mb-2">
                                Nombre de la Licencia:
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name', $license->name) }}"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('name') border-red-500 @enderror"
                                required>
                            @error('name')
                                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="license_key" class="block text-gray-700 text-sm font-bold mb-2">
                                Clave de Licencia:
                            </label>
                            <input type="text" name="license_key" id="license_key" value="{{ $license->license_key }}"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 bg-gray-100 leading-tight"
                                disabled>
                            <p class="text-gray-600 text-xs italic mt-1">La clave de licencia no se puede modificar.</p>
                        </div>

                        <div class="mb-4">
                            <label for="domain" class="block text-gray-700 text-sm font-bold mb-2">
                                Dominio:
                            </label>
                            <input type="text" name="domain" id="domain" value="{{ old('domain', $license->domain) }}"
                                placeholder="ejemplo.com"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('domain') border-red-500 @enderror"
                                required>
                            @error('domain')
                                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="duration" class="block text-gray-700 text-sm font-bold mb-2">
                                Duración (meses):
                            </label>
                            <input type="number" name="duration" id="duration" value="{{ old('duration', $license->duration) }}"
                                min="1"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('duration') border-red-500 @enderror"
                                required>
                            @error('duration')
                                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $license->is_active) ? 'checked' : '' }}
                                    class="mr-2 leading-tight">
                                <span class="text-sm">
                                    Licencia activa
                                </span>
                            </label>
                        </div>

                        <div class="mb-4 bg-gray-50 p-4 rounded">
                            <p class="text-sm text-gray-700"><strong>Fecha de expiración:</strong> {{ $license->expiration_date->format('d/m/Y') }}</p>
                            <p class="text-sm text-gray-700"><strong>Días restantes:</strong> {{ abs($license->remaining_days) }} @if($license->remaining_days >= 0)días @else(expirada) @endif</p>
                            @if($license->last_checked_at)
                                <p class="text-sm text-gray-700"><strong>Última verificación:</strong> {{ $license->last_checked_at->format('d/m/Y H:i:s') }}</p>
                            @endif
                        </div>

                        <div class="flex items-center justify-between">
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Actualizar Licencia
                            </button>
                            <a href="{{ route('licenses.index') }}"
                                class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                                Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
