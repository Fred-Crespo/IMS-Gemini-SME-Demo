<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestión de Tickets de Soporte') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-md sm:rounded-lg">
                <div class="p-6">

                    <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0 md:space-x-4 mb-4">
                        
                        <div class="relative w-full md:w-1/3">
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/></svg>
                            </div>
                            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar por cliente, DNI o asunto..." class="block w-full p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-intinet-blue-500 focus:border-intinet-blue-500">
                        </div>
                        
                        <div class="flex items-center space-x-2 w-full md:w-auto">
                            
                            <div class="flex items-center p-1 space-x-1 bg-gray-100 rounded-lg">
                                <button wire:click="setFiltro('pendiente')" class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors {{ $filtro === 'pendiente' ? 'bg-white text-gray-800 shadow' : 'text-gray-600 hover:text-gray-800' }}">
                                    Pendientes
                                </button>
                                <button wire:click="setFiltro('vencido')" class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors {{ $filtro === 'vencido' ? 'bg-white text-gray-800 shadow' : 'text-gray-600 hover:text-gray-800' }}">
                                    Vencidos
                                </button>
                                <button wire:click="setFiltro('resuelto')" class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors {{ $filtro === 'resuelto' ? 'bg-white text-gray-800 shadow' : 'text-gray-600 hover:text-gray-800' }}">
                                    Resueltos
                                </button>
                            </div>

                            <div class="flex items-center space-x-2">
                                <button id="dropdownExportButton" data-dropdown-toggle="dropdownExportTickets" class="text-gray-700 bg-white border border-gray-300 hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 font-medium rounded-lg text-sm px-4 py-2.5 text-center inline-flex items-center" type="button">
                                    <svg class="w-4 h-4 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                                    Exportar
                                </button>
                                <div id="dropdownExportTickets" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44">
                                    <ul class="py-2 text-sm text-gray-700" aria-labelledby="dropdownExportButton">
                                      <li><a href="#" wire:click.prevent="exportPDF" class="block px-4 py-2 hover:bg-gray-100">PDF</a></li>
                                      <li><a href="#" wire:click.prevent="exportExcel" class="block px-4 py-2 hover:bg-gray-100">Excel</a></li>
                                      <li><a href="#" wire:click.prevent="exportCSV" class="block px-4 py-2 hover:bg-gray-100">CSV</a></li>
                                    </ul>
                                </div>
    
                                @can('crear tickets')
                                <button wire:click="create()" class="text-white bg-intinet-blue-600 hover:bg-intinet-blue-700 focus:ring-4 focus:outline-none focus:ring-intinet-blue-300 font-medium rounded-lg text-sm px-4 py-2.5 text-center inline-flex items-center">
                                    <svg class="w-4 h-4 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                                    Crear Ticket
                                </button>
                                @endcan
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-white uppercase bg-slate-800">
                                <tr>
                                    <th scope="col" class="px-6 py-3">ID</th>
                                    <th scope="col" class="px-6 py-3">Cliente</th>
                                    <th scope="col" class="px-6 py-3">DNI/RUC</th>
                                    <th scope="col" class="px-6 py-3">Celular</th>
                                    <th scope="col" class="px-6 py-3">Mapa</th>
                                    <th scope="col" class="px-6 py-3">F. Programada</th>
                                    <th scope="col" class="px-6 py-3">F. Apertura</th>
                                    <th scope="col" class="px-6 py-3">Prioridad</th>
                                    <th scope="col" class="px-6 py-3">Técnico</th>
                                    <th scope="col" class="px-6 py-3">Estado</th>
                                    <th scope="col" class="px-6 py-3">Ver/Editar/Eliminar</th>
                                    <th scope="col" class="px-6 py-3"><span class="sr-only">Acciones</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tickets as $ticket)
                                    <tr class="bg-white border-b hover:bg-gray-50" wire:key="ticket-{{ $ticket->id }}">
                                        <td class="px-6 py-4 font-medium text-gray-900">{{ $ticket->id }}</td>
                                        <td class="px-6 py-4">{{ $ticket->cliente->nombre_cliente }}</td>
                                        <td class="px-6 py-4">{{ $ticket->cliente->dni_ruc }}</td>
                                        <td class="px-6 py-4">{{ $ticket->cliente->celular }}</td>
                                        <td class="px-6 py-4">
                                            @if($ticket->cliente->latitud && $ticket->cliente->longitud)
                                                <a href="https://www.google.com/maps?q={{ $ticket->cliente->latitud }},{{ $ticket->cliente->longitud }}" target="_blank" class="text-blue-500 hover:underline">Ver Mapa</a>
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">{{ $ticket->f_programada ? \Carbon\Carbon::parse($ticket->f_programada)->format('d/m/Y H:i') : 'N/A' }}</td>
                                        <td class="px-6 py-4">{{ $ticket->f_apertura ? \Carbon\Carbon::parse($ticket->f_apertura)->format('d/m/Y H:i') : 'N/A' }}</td>
                                        <td class="px-6 py-4">
                                            <x-badge :color="match($ticket->prioridad) {
                                                'alta' => 'red', 'media' => 'yellow', 'baja' => 'green', default => 'gray'
                                            }">
                                                {{ ucfirst($ticket->prioridad) }}
                                            </x-badge>
                                        </td>
                                        <td class="px-6 py-4">{{ $ticket->tecnico->name ?? 'Sin asignar' }}</td>
                                        <td class="px-6 py-4">
                                            <x-badge :color="match($ticket->estado) {
                                                'resuelto' => 'green', 'vencido' => 'red', 'pendiente' => 'yellow', default => 'gray'
                                            }">
                                                {{ ucfirst($ticket->estado) }}
                                            </x-badge>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <!-- Botones de acción -->
                                            <div class="flex items-center justify-end space-x-2">
                                                <button wire:click="viewTicket({{ $ticket->id }})" title="Ver"><span class="text-blue-600">👁️</span></button>
                                                <button wire:click="edit({{ $ticket->id }})" title="Editar"><span class="text-yellow-600">✏️</span></button>
                                                <button wire:click="delete({{ $ticket->id }})" title="Eliminar"><span class="text-red-600">🗑️</span></button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="11" class="text-center px-6 py-4 text-gray-500">No hay tickets para mostrar.</td></tr>
                                @endforelse
                                </tbody>

                        </table>
                    </div>

                    <div class="mt-4">{{ $tickets->links() }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal "Crear/Editar Ticket" -->
    @if($showCreateEditModal)
        <div class="fixed z-10 inset-0 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75"></div>
                <div class="bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-lg sm:w-full">
                    <form wire:submit.prevent="save">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">{{ $ticket_id ? 'Editar' : 'Crear' }} Ticket</h3>
                            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Campos del formulario -->
                                <!-- Cliente -->
                                <div class="md:col-span-2">
                                    <label>Cliente</label>
                                    <select wire:model="cliente_id" class="mt-1 block w-full rounded-md border-gray-300">
                                        <option value="">Seleccione un cliente</option>
                                        @foreach($clientes as $cliente)
                                            <option value="{{ $cliente->id }}">{{ $cliente->nombre_cliente }}</option>
                                        @endforeach
                                    </select>
                                    @error('cliente_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                {{-- Asunto (editable o bloqueado, controlado por IA o usuario) --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Asunto</label>
                                    <div class="flex items-center space-x-2">
                                        <input type="text" 
       wire:model="asunto" {{-- Quita el .live para evitar peticiones extra mientras la IA escribe --}}
       class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50" 
       placeholder="Esperando clasificación..." 
       @if(!$editableAsunto) readonly @endif>
                                        <button type="button" 
                                                wire:click="$toggle('editableAsunto')" 
                                                class="text-xs text-blue-600 hover:text-blue-800 underline">
                                            {{ $editableAsunto ? 'Bloquear' : 'Editar' }}
                                        </button>
                                    </div>
                                    @error('asunto') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <!-- Descripción -->
                                <div class="md:col-span-2">
                                    <label>Descripción</label>
                                    <textarea wire:model="descripcion" rows="3" class="mt-1 block w-full rounded-md border-gray-300"></textarea>
                                    @error('descripcion') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <!-- Fecha -->
                                <div>
                                    <label>Fecha Programada</label>
                                    <input type="datetime-local" wire:model="f_programada" class="mt-1 block w-full rounded-md border-gray-300">
                                    @error('f_programada') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                {{-- Prioridad (manual o automática con IA) --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Prioridad</label>
                                    {{-- Cambia esto en tu select de Prioridad --}}
<select wire:model="prioridad" class="mt-1 block w-full rounded-md border-gray-300">
    <option value="">Seleccione prioridad</option> {{-- Quita el texto "(Detectar automáticamente)" del value --}}
    <option value="alta">Alta</option>
    <option value="media">Media</option>
    <option value="baja">Baja</option>
</select>
                                    @error('prioridad') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <!-- Técnico -->
                                <div>
                                    <label>Asignar Técnico</label>
                                    <select wire:model="tecnico_id" class="mt-1 block w-full rounded-md border-gray-300">
                                        <option value="">Sin asignar</option>
                                        @foreach($tecnicos as $tecnico)
                                            <option value="{{ $tecnico->id }}">{{ $tecnico->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- Estado -->
                                <div>
                                    <label>Estado</label>
                                    <select wire:model="estado" class="mt-1 block w-full rounded-md border-gray-300">
                                        <option value="pendiente">Pendiente</option>
                                        <option value="vencido">Vencido</option>
                                        <option value="resuelto">Resuelto</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-col sm:items-stretch space-y-2">

                            <!-- Botón de Clasificación con IA -->
                            <div>
                                <button type="button" wire:click="classifyWithAI" wire:loading.attr="disabled" class="w-full inline-flex justify-center items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-50 transition ease-in-out duration-150">
                                    <svg wire:loading wire:target="classifyWithAI" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span wire:loading.remove wire:target="classifyWithAI" class="inline-flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd" />
                                        </svg>
                                        Clasificar con IA
                                    </span>
                                    <span wire:loading wire:target="classifyWithAI">Clasificando...</span>
                                </button>
                            </div>

                            <!-- Botones Guardar y Cancelar -->
                            <div class="flex justify-end space-x-2">
                                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md">Guardar</button>
                                <button type="button" wire:click="closeModal()" class="bg-white text-gray-700 px-4 py-2 rounded-md border">Cancelar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal "Ver Ticket" -->
    @if($showViewModal && $selectedTicket)
    <div class="fixed z-10 inset-0 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="flex justify-between items-start">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Ticket N° {{ $selectedTicket->id }}
                        </h3>
                        <button wire:click="closeViewModal" class="text-gray-400 hover:text-gray-500">
                            <span class="sr-only">Close</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <div class="mt-4 border-t border-gray-200 pt-4">
                        <!-- Información del Cliente -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <span class="inline-flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 text-blue-800 font-bold">
                                    {{ strtoupper(substr($selectedTicket->cliente->nombre_cliente, 0, 2)) }}
                                </span>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $selectedTicket->cliente->nombre_cliente }}</div>
                                    <div class="text-sm text-gray-500">
                                        <span>DNI: {{ $selectedTicket->cliente->dni_ruc }}</span> | 
                                        <span>CEL: {{ $selectedTicket->cliente->celular }}</span>
                                    </div>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">{{ ucfirst($selectedTicket->prioridad) }}</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm text-gray-500">Creado {{ $selectedTicket->created_at->format('d/m/Y H:i') }}</div>
                                <div class="text-sm text-gray-500">Programado {{ \Carbon\Carbon::parse($selectedTicket->f_programada)->format('d/m/Y H:i') }}</div>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">{{ ucfirst($selectedTicket->estado) }}</span>
                            </div>
                        </div>

                        <!-- NUEVO: Asunto y Descripción -->
                        <div class="mt-4">
                            <h4 class="text-md font-semibold text-gray-800">Asunto: {{ $selectedTicket->asunto }}</h4>
                            <p class="text-sm text-gray-600 mt-1 whitespace-pre-wrap">{{ $selectedTicket->descripcion }}</p>
                        </div>

                        <!-- Pestañas -->
                        <div x-data="{ tab: 'respuestas' }" class="mt-4">
                            <div class="border-b border-gray-200">
                                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                                    <a href="#" @click.prevent="tab = 'respuestas'" :class="{ 'border-blue-500 text-blue-600': tab === 'respuestas' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                        Respuestas <span class="bg-gray-100 text-gray-600 ml-2 py-0.5 px-2.5 rounded-full text-xs">{{ $selectedTicket->respuestas->count() }}</span>
                                    </a>
                                    <a href="#" @click.prevent="tab = 'imagenes'" :class="{ 'border-blue-500 text-blue-600': tab === 'imagenes' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                        Imágenes <span class="bg-gray-100 text-gray-600 ml-2 py-0.5 px-2.5 rounded-full text-xs">{{ $selectedTicket->imagenes->count() }}</span>
                                    </a>
                                </nav>
                            </div>

                            <!-- Respuestas -->
                            <div x-show="tab === 'respuestas'" class="mt-4 space-y-4">
                                @forelse($selectedTicket->respuestas as $respuesta)
                                    <div class="flex space-x-3">
                                        <div class="flex-shrink-0">
                                            <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-gray-200 text-gray-600 font-bold">{{ strtoupper(substr($respuesta->user->name, 0, 1)) }}</span>
                                        </div>
                                        <div class="flex-1 space-y-1">
                                            <div class="flex items-center justify-between">
                                                <h3 class="text-sm font-medium">{{ $respuesta->user->name }}</h3>
                                                <div class="flex items-center space-x-2">
                                                    <p class="text-sm text-gray-500">{{ $respuesta->created_at->format('d/m/Y H:i') }}</p>
                                                    @can('eliminar respuestas ticket')
                                                        <button wire:click="deleteRespuesta({{ $respuesta->id }})" class="text-red-500 hover:text-red-700">
                                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                                        </button>
                                                    @endcan
                                                </div>
                                            </div>
                                            @if($editRespuestaId === $respuesta->id)
                                                <div>
                                                    <textarea wire:model.defer="editRespuestaMensaje" class="w-full border-gray-300 rounded-md"></textarea>
                                                    <div class="mt-2 space-x-2">
                                                        <button wire:click="updateRespuesta" class="text-sm bg-blue-500 text-white px-3 py-1 rounded">Guardar</button>
                                                        <button wire:click="$set('editRespuestaId', null)" class="text-sm bg-gray-300 px-3 py-1 rounded">Cancelar</button>
                                                    </div>
                                                </div>
                                            @else
                                                <p class="text-sm text-gray-600">{{ $respuesta->mensaje }}</p>
                                                @can('editar respuestas ticket')
                                                    <button wire:click="editRespuesta({{ $respuesta->id }})" class="text-blue-500 text-xs mt-1 hover:underline">Editar</button>
                                                @endcan
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-500">No hay respuestas para este ticket.</p>
                                @endforelse
                            </div>

                            <!-- Imágenes -->
                            <div x-show="tab === 'imagenes'" class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4">
                                @forelse($selectedTicket->imagenes as $imagen)
                                    <div class="relative group">
                                        @if($editImagenId === $imagen->id)
                                            <div class="col-span-2">
                                                <input type="file" wire:model="nuevaImagen">
                                                <div wire:loading wire:target="nuevaImagen">Subiendo...</div>
                                                <div class="mt-2 space-x-2">
                                                    <button wire:click="updateImagen" class="text-sm bg-blue-500 text-white px-3 py-1 rounded">Guardar</button>
                                                    <button wire:click="$set('editImagenId', null)" class="text-sm bg-gray-300 px-3 py-1 rounded">Cancelar</button>
                                                </div>
                                            </div>
                                        @else
                                            <a href="{{ asset('storage/' . $imagen->ruta_imagen) }}" target="_blank">
                                                <img src="{{ asset('storage/' . $imagen->ruta_imagen) }}" alt="Imagen de ticket" class="rounded-lg object-cover h-24 w-full">
                                            </a>
                                            <div class="absolute top-1 right-1 flex space-x-1">
                                                @can('editar imagenes ticket')
                                                    <button wire:click="editImagen({{ $imagen->id }})" class="bg-blue-500 text-white rounded-full p-1 text-xs">✏️</button>
                                                @endcan
                                                @can('eliminar imagenes ticket')
                                                    <button wire:click="deleteImagen({{ $imagen->id }})" class="bg-red-500 text-white rounded-full p-1 text-xs">🗑️</button>
                                                @endcan
                                            </div>
                                        @endif
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-500 col-span-4">No hay imágenes para este ticket.</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- Formularios de respuesta y carga de imagen -->
                        <div class="mt-6 border-t pt-4">
                            @can('responder tickets')
                                <form wire:submit.prevent="addRespuesta">
                                    <label for="nuevaRespuesta" class="block text-sm font-medium text-gray-700">Añadir Respuesta</label>
                                    <textarea wire:model="nuevaRespuesta" id="nuevaRespuesta" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                                    <button type="submit" class="mt-2 bg-blue-500 text-white px-4 py-2 rounded text-sm">Enviar</button>
                                </form>
                            @endcan

                            @can('subir imagenes ticket')
                                <form wire:submit.prevent="addImagenes" class="mt-4">
                                    <label for="nuevasImagenes" class="block text-sm font-medium text-gray-700">Subir Imágenes (Técnico)</label>
                                    <input type="file" wire:model="nuevasImagenes" id="nuevasImagenes" multiple class="mt-1 block w-full">
                                    <div wire:loading wire:target="nuevasImagenes">Subiendo...</div>
                                    <button type="submit" class="mt-2 bg-green-500 text-white px-4 py-2 rounded text-sm">Subir</button>
                                </form>
                            @endcan
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" wire:click="closeViewModal" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal "Opciones" -->
    @if($showOptionsMenu && $selectedTicket)
    <div class="fixed z-10 inset-0 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-sm sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="flex justify-between items-start">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Ticket N° {{ $selectedTicket->id }}
                        </h3>
                        <button wire:click="closeOptionsMenu" class="text-gray-400 hover:text-gray-500">
                            <span class="sr-only">Close</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    <div class="mt-4 text-center">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-gray-100">
                            <!-- Icono de Ticket -->
                            <svg class="h-6 w-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                        </div>
                        <h4 class="text-md font-medium text-gray-800 mt-2">Ticket Soporte</h4>
                        <div class="mt-4 flex">
                            <span class="inline-flex items-center px-3 border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm rounded-l-md">+51</span>
                            <input type="text" readonly value="{{ $selectedTicket->cliente->celular }}" class="flex-1 block w-full rounded-none border-gray-300">
                            <a href="https://wa.me/51{{ $selectedTicket->cliente->celular }}" target="_blank" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-r-md text-white bg-green-500 hover:bg-green-600">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M12.25 10a2.25 2.25 0 01-2.25 2.25H4.883c-.346 0-.625-.28-.625-.625V8.375c0-.346.28-.625.625-.625h5.117a2.25 2.25 0 012.25 2.25z"></path><path fill-rule="evenodd" d="M.001 8.544c0-4.393 3.56-7.952 7.952-7.952 2.13 0 4.1.833 5.62 2.333a7.91 7.91 0 012.334 5.62c0 4.393-3.56 7.952-7.953 7.952a8.012 8.012 0 01-2.91-.55l-2.883.823.823-2.883a8.012 8.012 0 01-.55-2.911zM14.8 13.19a6.657 6.657 0 00-9.414-9.414 6.657 6.657 0 009.414 9.414z" clip-rule="evenodd"></path></svg>
                                Enviar
                            </a>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" wire:click="closeOptionsMenu" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>
