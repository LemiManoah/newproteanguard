<?php

namespace App\Livewire\Operations;

use App\Enums\BillingCycle;
use App\Enums\ScheduleType;
use App\Models\Client;
use App\Models\ClientCategory;
use App\Models\ClientDocument;
use App\Services\AuditService;
use App\Services\PermissionService;
use App\Support\TenantContext;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Symfony\Component\HttpFoundation\Response;

#[Title('Client Form')]
class ClientFormPage extends Component
{
    use WithFileUploads;

    public ?int $clientId = null;

    public ?int $categoryId = null;

    public string $name = '';

    public ?string $contact1 = null;

    public ?string $contact2 = null;

    public ?string $email = null;

    public ?string $idNo = null;

    public ?string $tin = null;

    public ?string $vatNo = null;

    public ?string $address = null;

    public ?int $billingCycle = null;

    public string $amount = '0';

    public string $noGuards = '1';

    public ?string $billStart = null;

    public ?int $scheduleType = null;

    public string $activeTab = 'profile';

    public bool $showUploadModal = false;

    public string $documentTitle = '';

    public ?int $documentType = null;

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

    public function mount(?Client $client = null): void
    {
        if ($client?->exists) {
            abort_unless($client->businessId === $this->tenant->businessId(), Response::HTTP_NOT_FOUND);
            abort_unless($this->permissions->can($this->tenant->user(), 'view_clients'), Response::HTTP_FORBIDDEN);

            $this->clientId = $client->getKey();
            $this->categoryId = $client->categoryId;
            $this->name = (string) $client->name;
            $this->contact1 = $client->contact1;
            $this->contact2 = $client->contact2;
            $this->email = $client->email;
            $this->idNo = $client->id_no;
            $this->tin = $client->tin;
            $this->vatNo = $client->vat_no;
            $this->address = $client->address;
            $this->billingCycle = $client->billing_cycle->value;
            $this->amount = (string) $client->amount;
            $this->noGuards = (string) $client->no_guards;
            $this->billStart = $client->bill_start?->toDateString();
            $this->scheduleType = $client->schedule_type->value;

            return;
        }

        abort_unless($this->permissions->can($this->tenant->user(), 'add_client'), Response::HTTP_FORBIDDEN);

        $this->billStart = now()->toDateString();
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'categoryId' => ['required', 'integer'],
            'name' => ['required', 'string', 'max:255'],
            'contact1' => ['nullable', 'string', 'max:255'],
            'contact2' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'idNo' => ['nullable', 'string', 'max:255'],
            'tin' => ['nullable', 'string', 'max:255'],
            'vatNo' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'billingCycle' => ['required', 'integer', Rule::in(array_column(BillingCycle::cases(), 'value'))],
            'amount' => ['required', 'numeric', 'min:0'],
            'noGuards' => ['required', 'numeric', 'min:0'],
            'billStart' => ['nullable', 'date'],
            'scheduleType' => ['required', 'integer', Rule::in(array_column(ScheduleType::cases(), 'value'))],
        ];
    }

    public function save(): void
    {
        abort_unless(
            $this->clientId
                ? $this->permissions->can($this->tenant->user(), 'edit_clients')
                : $this->permissions->can($this->tenant->user(), 'add_client'),
            Response::HTTP_FORBIDDEN
        );

        $validated = $this->validate();

        ClientCategory::query()
            ->where('businessId', $this->tenant->businessId())
            ->findOrFail($validated['categoryId']);

        $client = $this->clientId
            ? Client::query()->where('businessId', $this->tenant->businessId())->findOrFail($this->clientId)
            : new Client;

        $client->forceFill([
            'categoryId' => $validated['categoryId'],
            'name' => $validated['name'],
            'contact1' => $validated['contact1'],
            'contact2' => $validated['contact2'],
            'email' => $validated['email'],
            'id_no' => $validated['idNo'],
            'tin' => $validated['tin'],
            'vat_no' => $validated['vatNo'],
            'address' => $validated['address'],
            'billing_cycle' => $validated['billingCycle'],
            'amount' => $validated['amount'],
            'no_guards' => $validated['noGuards'],
            'bill_start' => $validated['billStart'],
            'schedule_type' => $validated['scheduleType'],
            'userId' => $this->tenant->user()->getKey(),
            'businessId' => $this->tenant->businessId(),
        ]);
        $client->save();

        $this->audit->record("Saved client {$client->name}", $this->tenant->user());

        Flux::toast(variant: 'success', text: __('Client saved successfully.'));

        $this->redirectRoute('clients.edit', ['client' => $client], navigate: true);
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function openUpload(): void
    {
        abort_unless($this->clientId && $this->permissions->can($this->tenant->user(), 'edit_clients'), Response::HTTP_FORBIDDEN);

        $this->reset('documentTitle', 'document');
        $this->documentType = null;
        $this->showUploadModal = true;
    }

    public function saveDocument(): void
    {
        abort_unless($this->clientId && $this->permissions->can($this->tenant->user(), 'edit_clients'), Response::HTTP_FORBIDDEN);

        $validated = $this->validate([
            'documentTitle' => ['required', 'string', 'max:255'],
            'documentType' => ['required', 'integer', 'between:0,1'],
            'document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,webp,doc,docx', 'max:5120'],
        ]);

        $client = Client::query()
            ->where('businessId', $this->tenant->businessId())
            ->findOrFail($this->clientId);

        /** @var TemporaryUploadedFile $file */
        $file = $validated['document'];
        $path = $file->store((string) $client->getKey(), 'client_documents');

        $document = new ClientDocument;
        $document->forceFill([
            'clientId' => $client->getKey(),
            'title' => $validated['documentTitle'],
            'type' => $validated['documentType'],
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
        $this->reset('documentTitle', 'document');
        $this->documentType = null;

        Flux::toast(variant: 'success', text: __('Client document uploaded successfully.'));
    }

    public function render(): View
    {
        return view('livewire.operations.client-form-page', [
            'client' => $this->clientId
                ? Client::query()
                    ->with(['category', 'guards.securityGuard', 'activeGuards.securityGuard', 'activeDocuments', 'attendances.securityGuard'])
                    ->where('businessId', $this->tenant->businessId())
                    ->find($this->clientId)
                : null,
            'categories' => ClientCategory::query()
                ->where('businessId', $this->tenant->businessId())
                ->where('status', true)
                ->orderBy('name')
                ->get(),
            'billingCycles' => BillingCycle::cases(),
            'scheduleTypes' => ScheduleType::cases(),
            'canEdit' => $this->permissions->can($this->tenant->user(), 'edit_clients'),
        ]);
    }
}
