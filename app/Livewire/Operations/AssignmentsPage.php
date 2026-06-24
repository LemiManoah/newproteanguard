<?php

namespace App\Livewire\Operations;

use App\Enums\ScheduleType;
use App\Models\Client;
use App\Models\ClientGuard;
use App\Models\SecurityGuard;
use App\Services\AuditService;
use App\Services\PermissionService;
use App\Support\TenantContext;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Symfony\Component\HttpFoundation\Response;

#[Title('Guard Assignments')]
class AssignmentsPage extends Component
{
    public ?int $clientId = null;

    public ?int $guardId = null;

    public ?string $from = null;

    public ?int $scheduleType = null;

    protected TenantContext $tenant;

    protected PermissionService $permissions;

    protected AuditService $audit;

    public function boot(TenantContext $tenant, PermissionService $permissions, AuditService $audit): void
    {
        $this->tenant = $tenant;
        $this->permissions = $permissions;
        $this->audit = $audit;
    }

    public function mount(): void
    {
        $this->from = now()->toDateString();
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'clientId' => ['required', 'integer'],
            'guardId' => ['required', 'integer'],
            'from' => ['required', 'date'],
            'scheduleType' => ['required', 'integer', Rule::in(array_column(ScheduleType::cases(), 'value'))],
        ];
    }

    public function assign(): void
    {
        abort_unless($this->permissions->can($this->tenant->user(), 'assign_guards'), Response::HTTP_FORBIDDEN);

        $validated = $this->validate();

        $alreadyAssigned = ClientGuard::query()
            ->where('businessId', $this->tenant->businessId())
            ->where('guardId', $validated['guardId'])
            ->where('status', true)
            ->exists();

        if ($alreadyAssigned) {
            Flux::toast(variant: 'danger', text: __('This guard already has an active deployment.'));

            return;
        }

        DB::transaction(function () use ($validated): void {
            $client = Client::query()
                ->where('businessId', $this->tenant->businessId())
                ->where('status', true)
                ->findOrFail($validated['clientId']);

            $guard = SecurityGuard::query()
                ->where('businessId', $this->tenant->businessId())
                ->where('status', true)
                ->findOrFail($validated['guardId']);

            $deployment = new ClientGuard;
            $deployment->forceFill([
                'clientId' => $client->getKey(),
                'guardId' => $guard->getKey(),
                'from' => $validated['from'],
                'schedule_type' => $validated['scheduleType'],
                'status' => true,
                'userId' => $this->tenant->user()->getKey(),
                'businessId' => $this->tenant->businessId(),
            ]);
            $deployment->save();

            $guard->forceFill(['assigned' => true])->save();
            $client->forceFill([
                'assigned' => true,
                'actual_guards' => ClientGuard::query()
                    ->where('businessId', $this->tenant->businessId())
                    ->where('clientId', $client->getKey())
                    ->where('status', true)
                    ->count(),
            ])->save();

            $this->audit->record("Assigned guard {$guard->code} to client {$client->name}", $this->tenant->user());
        });

        $this->reset('guardId', 'clientId', 'scheduleType');
        $this->from = now()->toDateString();

        Flux::toast(variant: 'success', text: __('Guard assigned successfully.'));
    }

    public function render(): View
    {
        abort_unless($this->permissions->can($this->tenant->user(), 'assign_guards'), Response::HTTP_FORBIDDEN);

        return view('livewire.operations.assignments-page', [
            'clients' => Client::query()
                ->where('businessId', $this->tenant->businessId())
                ->where('status', true)
                ->whereRaw('(select count(*) from client_guards where client_guards.clientId = clients.id and client_guards.status = 1) < clients.no_guards')
                ->orderBy('name')
                ->get(),
            'guards' => SecurityGuard::query()
                ->where('businessId', $this->tenant->businessId())
                ->where('status', true)
                ->where('assigned', false)
                ->orderBy('code')
                ->get(),
            'deployments' => ClientGuard::query()
                ->with(['client', 'securityGuard'])
                ->where('businessId', $this->tenant->businessId())
                ->where('status', true)
                ->latest()
                ->get(),
            'scheduleTypes' => ScheduleType::cases(),
        ]);
    }
}
