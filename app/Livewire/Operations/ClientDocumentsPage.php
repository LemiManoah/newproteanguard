<?php

namespace App\Livewire\Operations;

use App\Models\Client;
use App\Models\ClientDocument;
use App\Services\AuditService;
use App\Services\PermissionService;
use App\Support\TenantContext;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Symfony\Component\HttpFoundation\Response;

#[Title('Client Documents')]
class ClientDocumentsPage extends Component
{
    use WithFileUploads;

    public bool $showUploadModal = false;

    public ?int $clientId = null;

    public string $title = '';

    public int $type = 3;

    public ?TemporaryUploadedFile $document = null;

    protected TenantContext $tenant;

    protected PermissionService $permissions;

    protected AuditService $audit;

    public function boot(TenantContext $tenant, PermissionService $permissions, AuditService $audit): void
    {
        $this->tenant = $tenant;
        $this->permissions = $permissions;
        $this->audit = $audit;
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'clientId' => ['required', 'integer'],
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'integer', 'between:0,3'],
            'document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,webp,doc,docx', 'max:5120'],
        ];
    }

    public function openUpload(): void
    {
        abort_unless($this->permissions->can($this->tenant->user(), 'edit_clients'), Response::HTTP_FORBIDDEN);

        $this->reset('clientId', 'title', 'document');
        $this->type = 3;
        $this->showUploadModal = true;
    }

    public function save(): void
    {
        abort_unless($this->permissions->can($this->tenant->user(), 'edit_clients'), Response::HTTP_FORBIDDEN);

        $validated = $this->validate();

        $client = Client::query()
            ->where('businessId', $this->tenant->businessId())
            ->findOrFail($validated['clientId']);

        /** @var TemporaryUploadedFile $file */
        $file = $validated['document'];
        $path = $file->store((string) $client->getKey(), 'client_documents');

        $document = new ClientDocument;
        $document->forceFill([
            'clientId' => $client->getKey(),
            'title' => $validated['title'],
            'type' => $validated['type'],
            'disk' => 'client_documents',
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'status' => true,
            'userId' => $this->tenant->user()->getKey(),
            'businessId' => $this->tenant->businessId(),
        ]);
        $document->save();

        $this->audit->record("Uploaded client document {$document->title} for {$client->name}", $this->tenant->user());

        $this->showUploadModal = false;
        $this->reset('clientId', 'title', 'document');
        $this->type = 3;
    }

    public function render(): View
    {
        abort_unless($this->permissions->can($this->tenant->user(), 'view_clients'), Response::HTTP_FORBIDDEN);

        return view('livewire.operations.client-documents-page', [
            'clients' => Client::query()
                ->where('businessId', $this->tenant->businessId())
                ->where('status', true)
                ->orderBy('name')
                ->get(),
            'documents' => ClientDocument::query()
                ->with('client')
                ->where('businessId', $this->tenant->businessId())
                ->latest()
                ->get(),
            'canUpload' => $this->permissions->can($this->tenant->user(), 'edit_clients'),
        ]);
    }
}
