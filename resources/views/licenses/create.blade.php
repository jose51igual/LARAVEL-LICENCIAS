<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Nueva Licencia') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('licenses.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="name" class="block text-gray-700 text-sm font-bold mb-2">
                                Nombre de la Licencia:
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('name') border-red-500 @enderror"
                                required>
                            @error('name')
                                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4 bg-blue-50 border border-blue-200 rounded p-4">
                            <p class="text-blue-700 text-sm">
                                <strong>📝 Nota:</strong> La clave de licencia se generará automáticamente al crear la licencia.
                            </p>
                        </div>

                        <div class="mb-4">
                            <label for="domain" class="block text-gray-700 text-sm font-bold mb-2">
                                Dominio:
                            </label>
                            <input type="text" name="domain" id="domain" value="{{ old('domain') }}"
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
                            <input type="number" name="duration" id="duration" value="{{ old('duration', 12) }}"
                                min="1"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('duration') border-red-500 @enderror"
                                required>
                            @error('duration')
                                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-between">
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Crear Licencia
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
