<?php

namespace App\Livewire\Operations;

use App\Models\GuardReferee;
use App\Models\SecurityGuard;
use App\Services\AuditService;
use App\Services\PermissionService;
use App\Support\TenantContext;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;
use Symfony\Component\HttpFoundation\Response;

#[Title('Guard Referees')]
class GuardRefereesPage extends Component
{
    public bool $showFormModal = false;

    public ?int $editingId = null;

    public ?int $guardId = null;

    public string $name = '';

    public ?string $contact = null;

    public ?string $residence = null;

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
            'name' => ['required', 'string', 'max:255'],
            'contact' => ['nullable', 'string', 'max:255'],
            'residence' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function create(): void
    {
        abort_unless($this->permissions->can($this->tenant->user(), 'edit_guards'), Response::HTTP_FORBIDDEN);

        $this->reset('editingId', 'guardId', 'name', 'contact', 'residence');
        $this->showFormModal = true;
    }

    public function edit(int $refereeId): void
    {
        abort_unless($this->permissions->can($this->tenant->user(), 'edit_guards'), Response::HTTP_FORBIDDEN);

        $referee = GuardReferee::query()
            ->where('businessId', $this->tenant->businessId())
            ->findOrFail($refereeId);

        $this->editingId = $referee->getKey();
        $this->guardId = $referee->guardId;
        $this->name = (string) $referee->name;
        $this->contact = $referee->contact;
        $this->residence = $referee->residence;
        $this->showFormModal = true;
    }

    public function save(): void
    {
        abort_unless($this->permissions->can($this->tenant->user(), 'edit_guards'), Response::HTTP_FORBIDDEN);

        $validated = $this->validate();

        SecurityGuard::query()
            ->where('businessId', $this->tenant->businessId())
            ->findOrFail($validated['guardId']);

        $referee = $this->editingId
            ? GuardReferee::query()->where('businessId', $this->tenant->businessId())->findOrFail($this->editingId)
            : new GuardReferee;

        $referee->forceFill([
            'guardId' => $validated['guardId'],
            'name' => $validated['name'],
            'contact' => $validated['contact'],
            'residence' => $validated['residence'],
            'status' => true,
            'userId' => $this->tenant->user()->getKey(),
            'businessId' => $this->tenant->businessId(),
        ]);
        $referee->save();

        $this->audit->record("Saved guard referee {$referee->name}", $this->tenant->user());

        $this->showFormModal = false;
        $this->reset('editingId', 'guardId', 'name', 'contact', 'residence');
    }

    public function render(): View
    {
        abort_unless($this->permissions->can($this->tenant->user(), 'view_guards'), Response::HTTP_FORBIDDEN);

        return view('livewire.operations.guard-referees-page', [
            'guards' => SecurityGuard::query()
                ->where('businessId', $this->tenant->businessId())
                ->where('status', true)
                ->orderBy('code')
                ->get(),
            'referees' => GuardReferee::query()
                ->with('securityGuard')
                ->where('businessId', $this->tenant->businessId())
                ->latest()
                ->get(),
            'canEdit' => $this->permissions->can($this->tenant->user(), 'edit_guards'),
        ]);
    }
}
