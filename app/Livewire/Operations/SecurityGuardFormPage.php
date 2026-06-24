<?php

namespace App\Livewire\Operations;

use App\Enums\GuardGender;
use App\Enums\IdentityDocumentType;
use App\Enums\LifeStatus;
use App\Enums\MaritalStatus;
use App\Models\GuardDocument;
use App\Models\GuardReferee;
use App\Models\SecurityGuard;
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

#[Title('Guard Form')]
class SecurityGuardFormPage extends Component
{
    use WithFileUploads;

    public ?int $guardId = null;

    public string $activeTab = 'profile';

    public ?int $codeNumber = null;

    public ?string $code = null;

    public string $fname = '';

    public string $lname = '';

    public ?string $contact1 = null;

    public ?string $contact2 = null;

    public ?string $email = null;

    public ?string $dob = null;

    public ?string $joinDate = null;

    public ?int $gender = null;

    public ?string $weight = null;

    public ?string $height = null;

    public ?string $nationality = null;

    public ?string $religion = null;

    public ?string $tribe = null;

    public ?int $maritalStatus = null;

    public ?string $address = null;

    public ?string $homeContact = null;

    public ?string $homeLocation = null;

    public ?string $fatherName = null;

    public ?string $fatherContact = null;

    public ?string $fatherOccupation = null;

    public ?int $fatherLifeStatus = null;

    public ?string $motherName = null;

    public ?string $motherContact = null;

    public ?string $motherOccupation = null;

    public ?int $motherLifeStatus = null;

    public ?string $nok = null;

    public ?string $nokContact = null;

    public ?string $nokRelationship = null;

    public ?string $nokResidence = null;

    public ?int $idType = null;

    public ?string $idNo = null;

    public ?string $idExpiry = null;

    public ?string $languages = null;

    public bool $medicalHistory = false;

    public ?string $medicalHistoryDetails = null;

    public bool $showUploadModal = false;

    public ?string $documentTitle = null;

    public ?int $documentType = null;

    public ?TemporaryUploadedFile $document = null;

    public bool $showRefereeModal = false;

    public ?int $editingRefereeId = null;

    public ?string $refereeName = null;

    public ?string $refereeContact = null;

    public ?string $refereeResidence = null;

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
            abort_unless($this->permissions->can($this->tenant->user(), 'view_guards'), Response::HTTP_FORBIDDEN);

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
            $this->gender = $guard->gender?->value;
            $this->weight = $guard->weight !== null ? (string) $guard->weight : null;
            $this->height = $guard->height !== null ? (string) $guard->height : null;
            $this->nationality = $guard->nationality;
            $this->religion = $guard->religion;
            $this->tribe = $guard->tribe;
            $this->maritalStatus = $guard->marital_status?->value;
            $this->address = $guard->address;
            $this->homeContact = $guard->home_contact;
            $this->homeLocation = $guard->home_location;
            $this->fatherName = $guard->father_name;
            $this->fatherContact = $guard->father_contact;
            $this->fatherOccupation = $guard->father_occupation;
            $this->fatherLifeStatus = $guard->fdeath_status?->value;
            $this->motherName = $guard->mother_name;
            $this->motherContact = $guard->mother_contact;
            $this->motherOccupation = $guard->mother_occupation;
            $this->motherLifeStatus = $guard->mdeath_status?->value;
            $this->nok = $guard->nok;
            $this->nokContact = $guard->nok_contact;
            $this->nokRelationship = $guard->nok_relationship;
            $this->nokResidence = $guard->nok_residence;
            $this->idType = $guard->id_type?->value;
            $this->idNo = $guard->id_no;
            $this->idExpiry = $guard->id_expiry?->toDateString();
            $this->languages = $guard->languages;
            $this->medicalHistory = (bool) $guard->medical_history;
            $this->medicalHistoryDetails = $guard->medical_history_details;

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
            'weight' => ['nullable', 'numeric', 'min:0'],
            'height' => ['nullable', 'numeric', 'min:0'],
            'nationality' => ['nullable', 'string', 'max:255'],
            'religion' => ['nullable', 'string', 'max:255'],
            'tribe' => ['nullable', 'string', 'max:255'],
            'maritalStatus' => ['required', 'integer', Rule::in(array_column(MaritalStatus::cases(), 'value'))],
            'address' => ['nullable', 'string', 'max:255'],
            'homeContact' => ['nullable', 'string', 'max:255'],
            'homeLocation' => ['nullable', 'string', 'max:255'],
            'fatherName' => ['nullable', 'string', 'max:255'],
            'fatherContact' => ['nullable', 'string', 'max:255'],
            'fatherOccupation' => ['nullable', 'string', 'max:255'],
            'fatherLifeStatus' => ['required', 'integer', Rule::in(array_column(LifeStatus::cases(), 'value'))],
            'motherName' => ['nullable', 'string', 'max:255'],
            'motherContact' => ['nullable', 'string', 'max:255'],
            'motherOccupation' => ['nullable', 'string', 'max:255'],
            'motherLifeStatus' => ['required', 'integer', Rule::in(array_column(LifeStatus::cases(), 'value'))],
            'nok' => ['nullable', 'string', 'max:255'],
            'nokContact' => ['nullable', 'string', 'max:255'],
            'nokRelationship' => ['nullable', 'string', 'max:255'],
            'nokResidence' => ['nullable', 'string', 'max:255'],
            'idType' => ['required', 'integer', Rule::in(array_column(IdentityDocumentType::cases(), 'value'))],
            'idNo' => ['nullable', 'string', 'max:255'],
            'idExpiry' => ['nullable', 'date'],
            'languages' => ['nullable', 'string', 'max:255'],
            'medicalHistory' => ['boolean'],
            'medicalHistoryDetails' => ['nullable', 'string', 'max:255'],
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
            'weight' => $validated['weight'],
            'height' => $validated['height'],
            'join_date' => $validated['joinDate'],
            'gender' => $validated['gender'],
            'nationality' => $validated['nationality'],
            'religion' => $validated['religion'],
            'tribe' => $validated['tribe'],
            'marital_status' => $validated['maritalStatus'],
            'address' => $validated['address'],
            'home_contact' => $validated['homeContact'],
            'home_location' => $validated['homeLocation'],
            'father_name' => $validated['fatherName'],
            'father_contact' => $validated['fatherContact'],
            'father_occupation' => $validated['fatherOccupation'],
            'fdeath_status' => $validated['fatherLifeStatus'],
            'mother_name' => $validated['motherName'],
            'mother_contact' => $validated['motherContact'],
            'mother_occupation' => $validated['motherOccupation'],
            'mdeath_status' => $validated['motherLifeStatus'],
            'nok' => $validated['nok'],
            'nok_contact' => $validated['nokContact'],
            'nok_relationship' => $validated['nokRelationship'],
            'nok_residence' => $validated['nokResidence'],
            'id_type' => $validated['idType'],
            'id_no' => $validated['idNo'],
            'id_expiry' => $validated['idExpiry'],
            'languages' => $validated['languages'],
            'medical_history' => $validated['medicalHistory'],
            'medical_history_details' => $validated['medicalHistory'] ? $validated['medicalHistoryDetails'] : null,
            'userId' => $this->tenant->user()->getKey(),
            'businessId' => $this->tenant->businessId(),
        ]);
        $guard->save();

        $this->audit->record("Saved security guard {$guard->code}", $this->tenant->user());

        Flux::toast(variant: 'success', text: __('Security guard saved.'));

        $this->redirectRoute($this->guardId ? 'guards.edit' : 'guards.index', $this->guardId ? ['guard' => $guard] : [], navigate: true);
    }

    public function openUpload(): void
    {
        abort_unless($this->guardId && $this->permissions->can($this->tenant->user(), 'edit_guards'), Response::HTTP_FORBIDDEN);

        $this->reset('documentTitle', 'documentType', 'document');
        $this->showUploadModal = true;
    }

    public function saveDocument(): void
    {
        abort_unless($this->guardId && $this->permissions->can($this->tenant->user(), 'edit_guards'), Response::HTTP_FORBIDDEN);

        $validated = $this->validate([
            'documentTitle' => ['required', 'string', 'max:255'],
            'documentType' => ['required', 'integer', 'between:0,3'],
            'document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,webp,doc,docx', 'max:5120'],
        ]);

        $guard = SecurityGuard::query()
            ->where('businessId', $this->tenant->businessId())
            ->findOrFail($this->guardId);

        /** @var TemporaryUploadedFile $file */
        $file = $validated['document'];
        $path = $file->store((string) $guard->getKey(), 'guard_documents');

        $document = new GuardDocument;
        $document->forceFill([
            'guardId' => $guard->getKey(),
            'title' => $validated['documentTitle'],
            'type' => $validated['documentType'],
            'disk' => 'guard_documents',
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'status' => true,
            'userId' => $this->tenant->user()->getKey(),
            'businessId' => $this->tenant->businessId(),
        ])->save();

        $this->audit->record("Uploaded guard document {$document->title} for {$guard->code}", $this->tenant->user());

        $this->showUploadModal = false;
        $this->reset('documentTitle', 'documentType', 'document');

        Flux::toast(variant: 'success', text: __('Guard document uploaded.'));
    }

    public function verifyDocuments(): void
    {
        abort_unless($this->guardId && $this->permissions->can($this->tenant->user(), 'edit_guards'), Response::HTTP_FORBIDDEN);

        $guard = SecurityGuard::query()
            ->where('businessId', $this->tenant->businessId())
            ->findOrFail($this->guardId);

        $guard->forceFill(['doc_verified' => ! $guard->doc_verified])->save();

        Flux::toast(variant: 'success', text: $guard->doc_verified ? __('Guard documents verified.') : __('Guard documents marked incomplete.'));
    }

    public function openReferee(?int $refereeId = null): void
    {
        abort_unless($this->guardId && $this->permissions->can($this->tenant->user(), 'edit_guards'), Response::HTTP_FORBIDDEN);

        $this->reset('editingRefereeId', 'refereeName', 'refereeContact', 'refereeResidence');

        if ($refereeId) {
            $referee = GuardReferee::query()
                ->where('businessId', $this->tenant->businessId())
                ->where('guardId', $this->guardId)
                ->findOrFail($refereeId);

            $this->editingRefereeId = $referee->getKey();
            $this->refereeName = $referee->name;
            $this->refereeContact = $referee->contact;
            $this->refereeResidence = $referee->residence;
        }

        $this->showRefereeModal = true;
    }

    public function saveReferee(): void
    {
        abort_unless($this->guardId && $this->permissions->can($this->tenant->user(), 'edit_guards'), Response::HTTP_FORBIDDEN);

        $validated = $this->validate([
            'refereeName' => ['required', 'string', 'max:255'],
            'refereeContact' => ['nullable', 'string', 'max:255'],
            'refereeResidence' => ['nullable', 'string', 'max:255'],
        ]);

        $referee = $this->editingRefereeId
            ? GuardReferee::query()
                ->where('businessId', $this->tenant->businessId())
                ->where('guardId', $this->guardId)
                ->findOrFail($this->editingRefereeId)
            : new GuardReferee;

        $referee->forceFill([
            'guardId' => $this->guardId,
            'name' => $validated['refereeName'],
            'contact' => $validated['refereeContact'],
            'residence' => $validated['refereeResidence'],
            'status' => true,
            'userId' => $this->tenant->user()->getKey(),
            'businessId' => $this->tenant->businessId(),
        ])->save();

        $this->audit->record("Saved guard referee {$referee->name}", $this->tenant->user());

        $this->showRefereeModal = false;
        $this->reset('editingRefereeId', 'refereeName', 'refereeContact', 'refereeResidence');

        Flux::toast(variant: 'success', text: __('Guard referee saved.'));
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
            'lifeStatuses' => LifeStatus::cases(),
            'canEdit' => $this->permissions->can($this->tenant->user(), 'edit_guards'),
        ]);
    }
}
