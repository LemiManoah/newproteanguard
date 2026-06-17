<?php

namespace App\Livewire\Operations;

use App\Enums\GuardGender;
use App\Enums\IdentityDocumentType;
use App\Enums\MaritalStatus;
use App\Models\SecurityGuard;
use App\Services\AuditService;
use App\Services\PermissionService;
use App\Support\TenantContext;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Symfony\Component\HttpFoundation\Response;

#[Title('Guard Form')]
class SecurityGuardFormPage extends Component
{
    public ?int $guardId = null;

    public ?int $codeNumber = null;

    public ?string $code = null;

    public string $fname = '';

    public string $lname = '';

    public ?string $contact1 = null;

    public ?string $contact2 = null;

    public ?string $email = null;

    public ?string $dob = null;

    public ?string $joinDate = null;

    public int $gender = 0;

    public int $maritalStatus = 0;

    public ?string $address = null;

    public ?string $nok = null;

    public ?string $nokContact = null;

    public ?string $nokRelationship = null;

    public int $idType = 0;

    public ?string $idNo = null;

    public ?string $idExpiry = null;

    protected TenantContext $tenant;

    protected PermissionService $permissions;

    protected AuditService $audit;

    public function boot(TenantContext $tenant, PermissionService $permissions, AuditService $audit): void
    {
        $this->tenant = $tenant;
        $this->permissions = $permissions;
        $this->audit = $audit;
    }

    public function mount(?SecurityGuard $guard = null): void
    {
        if ($guard?->exists) {
            abort_unless($guard->businessId === $this->tenant->businessId(), Response::HTTP_NOT_FOUND);
            abort_unless($this->permissions->can($this->tenant->user(), 'edit_guards'), Response::HTTP_FORBIDDEN);

            $this->guardId = $guard->getKey();
            $this->codeNumber = $guard->code_number;
            $this->code = $guard->code;
            $this->fname = (string) $guard->fname;
            $this->lname = (string) $guard->lname;
            $this->contact1 = $guard->contact1;
            $this->contact2 = $guard->contact2;
            $this->email = $guard->email;
            $this->dob = $guard->dob?->toDateString();
            $this->joinDate = $guard->join_date?->toDateString();
            $this->gender = $guard->gender->value;
            $this->maritalStatus = $guard->marital_status->value;
            $this->address = $guard->address;
            $this->nok = $guard->nok;
            $this->nokContact = $guard->nok_contact;
            $this->nokRelationship = $guard->nok_relationship;
            $this->idType = $guard->id_type->value;
            $this->idNo = $guard->id_no;
            $this->idExpiry = $guard->id_expiry?->toDateString();

            return;
        }

        abort_unless($this->permissions->can($this->tenant->user(), 'add_guards'), Response::HTTP_FORBIDDEN);
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'codeNumber' => ['nullable', 'integer', 'min:0'],
            'code' => ['nullable', 'string', 'max:255'],
            'fname' => ['required', 'string', 'max:255'],
            'lname' => ['required', 'string', 'max:255'],
            'contact1' => ['nullable', 'string', 'max:255'],
            'contact2' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'dob' => ['nullable', 'date'],
            'joinDate' => ['nullable', 'date'],
            'gender' => ['required', 'integer', Rule::in(array_column(GuardGender::cases(), 'value'))],
            'maritalStatus' => ['required', 'integer', Rule::in(array_column(MaritalStatus::cases(), 'value'))],
            'address' => ['nullable', 'string', 'max:255'],
            'nok' => ['nullable', 'string', 'max:255'],
            'nokContact' => ['nullable', 'string', 'max:255'],
            'nokRelationship' => ['nullable', 'string', 'max:255'],
            'idType' => ['required', 'integer', Rule::in(array_column(IdentityDocumentType::cases(), 'value'))],
            'idNo' => ['nullable', 'string', 'max:255'],
            'idExpiry' => ['nullable', 'date'],
        ];
    }

    public function save(): void
    {
        abort_unless(
            $this->guardId
                ? $this->permissions->can($this->tenant->user(), 'edit_guards')
                : $this->permissions->can($this->tenant->user(), 'add_guards'),
            Response::HTTP_FORBIDDEN
        );

        $validated = $this->validate();

        $guard = $this->guardId
            ? SecurityGuard::query()->where('businessId', $this->tenant->businessId())->findOrFail($this->guardId)
            : new SecurityGuard;

        $guard->forceFill([
            'code_number' => $validated['codeNumber'],
            'code' => $validated['code'],
            'fname' => $validated['fname'],
            'lname' => $validated['lname'],
            'contact1' => $validated['contact1'],
            'contact2' => $validated['contact2'],
            'email' => $validated['email'],
            'dob' => $validated['dob'],
            'join_date' => $validated['joinDate'],
            'gender' => $validated['gender'],
            'marital_status' => $validated['maritalStatus'],
            'address' => $validated['address'],
            'nok' => $validated['nok'],
            'nok_contact' => $validated['nokContact'],
            'nok_relationship' => $validated['nokRelationship'],
            'id_type' => $validated['idType'],
            'id_no' => $validated['idNo'],
            'id_expiry' => $validated['idExpiry'],
            'userId' => $this->tenant->user()->getKey(),
            'businessId' => $this->tenant->businessId(),
        ]);
        $guard->save();

        $this->audit->record("Saved security guard {$guard->code}", $this->tenant->user());

        $this->redirectRoute('guards.index', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.operations.security-guard-form-page', [
            'guard' => $this->guardId
                ? SecurityGuard::query()
                    ->with(['activeClients.client', 'activeDocuments', 'activeReferees'])
                    ->where('businessId', $this->tenant->businessId())
                    ->find($this->guardId)
                : null,
            'genders' => GuardGender::cases(),
            'maritalStatuses' => MaritalStatus::cases(),
            'identityTypes' => IdentityDocumentType::cases(),
        ]);
    }
}
