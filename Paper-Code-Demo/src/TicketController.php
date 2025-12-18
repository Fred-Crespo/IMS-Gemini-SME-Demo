<?php

namespace App\Livewire\Ticket;

use Livewire\WithFileUploads;
use App\Models\TicketRespuesta;
use App\Models\TicketImagen;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Cliente;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
use App\Exports\TicketsExport;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use Illuminate\Support\Facades\Http;//IA
use Illuminate\Support\Facades\Log;

class Index extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $filtro = 'pendiente'; // pendiente, vencidos, resueltos, abierto
    public $tecnicos = [];
    public $clientes = [];
    public $search = '';

    // Propiedades para los modales
    public $showViewModal = false;
    public $showCreateEditModal = false;
    public $showConfirmModal = false;
    public $nuevaRespuesta = '';
    public $nuevasImagenes = [];
    public $editRespuestaId;
    public $editRespuestaMensaje = '';
    public $editImagenId;
    public $nuevaImagen;

    public $selectedTicket;
    public $ticket_id;
    public $ticketIdToDelete;
    public $showOptionsMenu = false;

    public $iaIsLoading = false;

    // Control del campo "Asunto" (IA o manual)
    public $editableAsunto = false;

    // [CAMBIO] 1. Campos del formulario (los inicializamos)
    public $cliente_id = '';
    public $tecnico_id = '';
    public $asunto = '';
    public $descripcion = '';
    public $f_programada = '';
    public $prioridad = ''; // Empezará en blanco
    public $estado = 'pendiente';

    protected function rules()
    {
        return [
            // El 'required' forzará al usuario a elegir un cliente
            'cliente_id' => 'required|exists:clientes,id', 
            'asunto' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'f_programada' => 'required|date',
            // El 'required' forzará a elegir una prioridad
            'prioridad' => 'nullable|in:baja,media,alta',
            'tecnico_id' => 'nullable|exists:users,id',
            'estado' => 'required|in:pendiente,vencido,resuelto',
        ];
    }

    // [CAMBIO] 2. Añadimos los mensajes de error
    protected $messages = [
        'cliente_id.required' => 'Debe seleccionar un cliente.',
        'asunto.required' => 'El asunto es obligatorio (o use la IA).',
        'descripcion.required' => 'La descripción es obligatoria.',
        'f_programada.required' => 'La fecha programada es obligatoria.',
        'prioridad.in' => 'Debe seleccionar una prioridad válida (o deje vacío para usar la IA).',
        // Mensajes para la subida de imágenes
        'nuevasImagenes.*.image' => 'Uno de los archivos no es una imagen válida (jpg, png, etc.).',
        'nuevasImagenes.*.max' => 'Uno de los archivos es demasiado grande (máx 2MB).',
    ];

    public function mount()
    {
        $this->tecnicos = User::role('Tecnico')->get();
        $this->clientes = Cliente::all();

        if (request()->query('estado') === 'abierto') {
            $this->filtro = 'abierto';
        }

        $this->editableAsunto = false; // Inicializamos el modo bloqueado
    }

    public function render()
    {
        $query = Ticket::with(['cliente', 'tecnico']);

        if (in_array($this->filtro, ['pendiente', 'vencido', 'resuelto'])) {
            $query->where('estado', $this->filtro);
        } elseif ($this->filtro === 'abierto') {
            $query->where('estado', '!=', 'resuelto');
        }

        $query->when($this->search, function ($q) {
            $q->where(function ($subq) {
                $subq->where('asunto', 'like', '%' . $this->search . '%')
                    ->orWhereHas('cliente', function ($clientq) {
                        $clientq->where('nombre_cliente', 'like', '%' . $this->search . '%')
                                ->orWhere('dni_ruc', 'like', '%' . $this->search . '%');
                    });
            });
        });

        $tickets = $query->paginate(10);

        return view('livewire.ticket.index', [
            'tickets' => $tickets,
            // Pasamos los clientes y tecnicos desde aquí para no volver a consultarlos
            'clientes' => $this->clientes, 
            'tecnicos' => $this->tecnicos
        ])->layout('layouts.app');
    }

    public function setFiltro($filtro)
    {
        $this->filtro = $filtro;
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->resetInputFields();
        $this->resetErrorBag(); // [CAMBIO] 3. Limpiamos errores
        $this->showCreateEditModal = true;
    }

    public function edit($ticketId)
    {
        $this->resetErrorBag(); // [CAMBIO] 3. Limpiamos errores
        $ticket = Ticket::findOrFail($ticketId);
        $this->ticket_id = $ticket->id;
        $this->selectedTicket = Ticket::find($ticketId);
        $this->cliente_id = $ticket->cliente_id;
        $this->tecnico_id = $ticket->tecnico_id;
        $this->asunto = $ticket->asunto;
        $this->descripcion = $ticket->descripcion;
        $this->f_programada = $ticket->f_programada ? \Carbon\Carbon::parse($ticket->f_programada)->format('Y-m-d\TH:i') : null;
        $this->prioridad = $ticket->prioridad;
        $this->estado = $ticket->estado;
        $this->showCreateEditModal = true;
    }

    public function save()
{
    // 1. Validar que los campos no estén vacíos
    $this->validate([
        'asunto' => 'required',
        'descripcion' => 'required',
        'prioridad' => 'required|in:baja,media,alta', // Asegura valores válidos para el ENUM
    ]);

    try {
        Ticket::updateOrCreate([
            'id' => $this->ticket_id
        ], [
            'asunto' => $this->asunto,
            'descripcion' => $this->descripcion,
            'f_programada' => $this->f_programada,
            'prioridad' => $this->prioridad ?? 'baja', // Si es nulo, pone 'baja' por defecto
            'estado' => $this->estado,
            'f_apertura' => ($this->ticket_id && $this->selectedTicket) 
                            ? $this->selectedTicket->f_apertura 
                            : now(),
        ]);

        $message = $this->ticket_id ? 'Ticket actualizado.' : 'Ticket creado.';
        $this->dispatch('notify', $message);
        $this->closeModal();

    } catch (\Exception $e) {
        Log::error("Error al guardar ticket: " . $e->getMessage());
        $this->dispatch('notify-error', 'Error al guardar en la base de datos.');
    }
}

    public function delete($ticketId)
    {
        $this->ticketIdToDelete = $ticketId;
        $this->showConfirmModal = true;
    }

    public function deleteConfirmed()
    {
        Ticket::find($this->ticketIdToDelete)->delete();
        $this->dispatch('notify', 'Ticket eliminado correctamente.');
        $this->showConfirmModal = false;
    }

    
     public function viewTicket($ticketId)
    {
        $this->selectedTicket = Ticket::with(['cliente', 'tecnico', 'respuestas.user', 'imagenes'])->findOrFail($ticketId);
        $this->showViewModal = true;
    }

    public function optionsMenu($ticketId)
    {
        $this->selectedTicket = Ticket::findOrFail($ticketId);
        $this->showOptionsMenu = true;
    }

    public function closeViewModal()
    {
        $this->showViewModal = false;
        $this->selectedTicket = null;
    }

    public function closeOptionsMenu()
    {
        $this->showOptionsMenu = false;
        $this->selectedTicket = null;
    }

    public function closeModal()
    {
        $this->showCreateEditModal = false;
        $this->resetInputFields();
        $this->resetErrorBag(); // [CAMBIO] 3. Limpiamos errores
    }

    private function resetInputFields()
    {
        $this->ticket_id = null;
        $this->cliente_id = '';
        $this->tecnico_id = ''; // Cambiado de null a ''
        $this->asunto = '';
        $this->descripcion = '';
        $this->f_programada = '';
        $this->prioridad = ''; // [CAMBIO] 4. Empezará en blanco
        $this->estado = 'pendiente';
    }

    public function addRespuesta()
    {
        $this->validate(['nuevaRespuesta' => 'required|string']);

        TicketRespuesta::create([
            'ticket_id' => $this->selectedTicket->id,
            'user_id' => Auth::id(),
            'mensaje' => $this->nuevaRespuesta,
        ]);

        $this->nuevaRespuesta = '';
        $this->selectedTicket->load('respuestas.user'); 
        $this->dispatch('notify', 'Respuesta añadida.');
    }

    public function addImagenes()
    {
        $this->validate(
            ['nuevasImagenes.*' => 'image|max:2048'],
            $this->messages
        );

        // ESTE BUCLE SE ESTÁ SALTANDO (PORQUE $this->nuevasImagenes ESTÁ VACÍO)
        foreach ($this->nuevasImagenes as $imagen) {
            $path = $imagen->store('ticket_images', 'public');
            TicketImagen::create([
                'ticket_id' => $this->selectedTicket->id,
                'ruta_imagen' => $path,
            ]);
        }

        $this->nuevasImagenes = []; 
        $this->selectedTicket->load('imagenes');
        // ...Y LA FUNCIÓN SALTA DIRECTO AQUÍ, MOSTRANDO EL "ÉXITO"
        $this->dispatch('notify', 'Imágenes subidas correctamente.'); 
    }

    public function editRespuesta($respuestaId)
    {
        $respuesta = TicketRespuesta::findOrFail($respuestaId);
        // abort_unless(auth()->user()->can('editar respuestas ticket'), 403);
        $this->editRespuestaId = $respuesta->id;
        $this->editRespuestaMensaje = $respuesta->mensaje;
    }

    public function updateRespuesta()
    {
        $this->validate(['editRespuestaMensaje' => 'required|string']);
        $respuesta = TicketRespuesta::findOrFail($this->editRespuestaId);
        // abort_unless(auth()->user()->can('editar respuestas ticket'), 403);
        $respuesta->update(['mensaje' => $this->editRespuestaMensaje]);
        $this->editRespuestaId = null;
        $this->editRespuestaMensaje = '';
        $this->selectedTicket->load('respuestas.user');
    }

    public function deleteRespuesta($respuestaId)
    {
        $respuesta = TicketRespuesta::findOrFail($respuestaId);
        $respuesta->delete();
        $this->selectedTicket->load('respuestas.user');
    }

    public function editImagen($imagenId)
    {
        $this->editImagenId = $imagenId;
    }

    public function updateImagen()
    {
        $this->validate(['nuevaImagen' => 'image|max:2048']);
        $imagen = TicketImagen::findOrFail($this->editImagenId);
        // abort_unless(auth()->user()->can('editar imagenes ticket'), 403);
        Storage::disk('public')->delete($imagen->ruta_imagen);
        $path = $this->nuevaImagen->store('ticket_images', 'public');
        $imagen->update(['ruta_imagen' => $path]);
        $this->editImagenId = null;
        $this->nuevaImagen = null;
        // [CAMBIO] Esto recarga la relación de imágenes
        $this->selectedTicket->load('imagenes');
    }

    public function deleteImagen($imagenId)
    {
        $imagen = TicketImagen::findOrFail($imagenId);
        Storage::disk('public')->delete($imagen->ruta_imagen);
        $imagen->delete();
        
        // [CAMBIO] Usamos refresh()
        $this->selectedTicket->load('imagenes');
    }

    public function exportPDF()
    {
        $tickets = $this->getFilteredTickets();
        $pdf = PDF::loadView('pdf.reporte_tickets', ['tickets' => $tickets]);
        return response()->streamDownload(fn() => print($pdf->output()), 'reporte-tickets.pdf');
    }

    public function exportExcel()
    {
        return Excel::download(new TicketsExport($this->search, $this->filtro), 'reporte-tickets.xlsx');
    }

    public function exportCSV()
    {
        return Excel::download(new TicketsExport($this->search, $this->filtro), 'reporte-tickets.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    private function getFilteredTickets()
    {
        $query = Ticket::with(['cliente', 'tecnico']);

        if (in_array($this->filtro, ['pendiente', 'vencido', 'resuelto'])) {
            $query->where('estado', $this->filtro);
        } elseif ($this->filtro === 'abierto') {
            $query->where('estado', '!=', 'resuelto');
        }

        return $query->when($this->search, function ($q) {
            $q->where(function ($subq) {
                $subq->where('asunto', 'like', '%' . $this->search . '%')
                    ->orWhereHas('cliente', function ($clientq) {
                        $clientq->where('nombre_cliente', 'like', '%' . $this->search . '%')
                                ->orWhere('dni_ruc', 'like', '%' . $this->search . '%');
                    });
            });
        })->get();
    }

    public function classifyWithAI()
{
    if (empty($this->descripcion)) {
        $this->dispatch('notify-error', 'Escriba una descripción primero.');
        return;
    }

    $this->iaIsLoading = true;

    try {
        $apiKey = config('services.gemini.api_key'); //en esta sección es donde llama al API de GEMINI, este codigo que nos brinda se coloca en el archivo .env
        
        $prompt = "Actúa como clasificador de tickets. Analiza: \"{$this->descripcion}\". 
        Responde SOLO un JSON: {\"prioridad\": \"baja|media|alta\", \"asunto\": \"Falla de Conexión|Consulta de Facturación|Problema de Equipo|Solicitud de Información|Problema de Velocidad\"}";

        // USAMOS EL ALIAS 'gemini-flash-latest' QUE EVITA EL ERROR 404
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key={$apiKey}";

        $response = Http::timeout(30)->post($url, [
            'contents' => [['parts' => [['text' => $prompt]]]]
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $responseText = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
            
            // Limpiar Markdown si la IA lo incluye
            $cleanJson = trim(str_replace(['```json', '```'], '', $responseText));
            $result = json_decode($cleanJson, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $this->prioridad = strtolower($result['prioridad']);
                $this->asunto = $result['asunto'];
                $this->dispatch('notify', 'IA: Clasificación completada.');
            } else {
                Log::error("JSON Inválido de IA: " . $responseText);
            }
        } else {
            // Si sale 429 aquí, es por la API Key nueva
            Log::error("Error API Gemini: " . $response->body());
            $this->dispatch('notify-error', 'La IA no pudo responder. Revise el log.');
        }
    } catch (\Exception $e) {
        Log::error("Excepción IA: " . $e->getMessage());
    } finally {
        $this->iaIsLoading = false;
    }
}
}