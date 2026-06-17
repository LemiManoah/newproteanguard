<?php

namespace App\Livewire\Operations;

use App\Models\GuardDocument;
use App\Models\SecurityGuard;
use App\Services\AuditService;
use App\Services\PermissionService;
use App\Support\TenantContext;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Symfony\Component\HttpFoundation\Response;

#[Title('Guard Documents')]
class GuardDocumentsPage extends Component
{
    use WithFileUploads;

    public bool $showUploadModal = false;

    public ?int $guardId = null;

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
            'guardId' => ['required', 'integer'],
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'integer', 'between:0,3'],
            'document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,webp,doc,docx', 'max:5120'],
        ];
    }

    public function openUpload(): void
    {
        abort_unless($this->permissions->can($this->tenant->user(), 'edit_guards'), Response::HTTP_FORBIDDEN);

        $this->reset('guardId', 'title', 'document');
        $this->type = 3;
        $this->showUploadModal = true;
    }

    public function save(): void
    {
        abort_unless($this->permissions->can($this->tenant->user(), 'edit_guards'), Response::HTTP_FORBIDDEN);

        $validated = $this->validate();

        $guard = SecurityGuard::query()
            ->where('businessId', $this->tenant->businessId())
            ->findOrFail($validated['guardId']);

        /** @var TemporaryUploadedFile $file */
        $file = $validated['document'];
        $path = $file->store((string) $guard->getKey(), 'guard_documents');

        $document = new GuardDocument;
        $document->forceFill([
            'guardId' => $guard->getKey(),
            'title' => $validated['title'],
            'type' => $validated['type'],
            'disk' => 'guard_documents',
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'status' => true,
            'userId' => $this->tenant->user()->getKey(),
            'businessId' => $this->tenant->businessId(),
        ]);
        $document->save();

        $this->audit->record("Uploaded guard document {$document->title} for {$guard->code}", $this->tenant->user());

        $this->showUploadModal = false;
        $this->reset('guardId', 'title', 'document');
        $this->type = 3;
    }

    public function render(): View
    {
        abort_unless($this->permissions->can($this->tenant->user(), 'view_guards'), Response::HTTP_FORBIDDEN);

        return view('livewire.operations.guard-documents-page', [
            'guards' => SecurityGuard::query()
                ->where('businessId', $this->tenant->businessId())
                ->where('status', true)
                ->orderBy('code')
                ->get(),
            'documents' => GuardDocument::query()
                ->with('securityGuard')
                ->where('businessId', $this->tenant->businessId())
                ->latest()
                ->get(),
            'canUpload' => $this->permissions->can($this->tenant->user(), 'edit_guards'),
        ]);
    }
}
