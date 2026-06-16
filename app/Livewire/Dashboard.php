<?php

namespace App\Livewire;

use App\Models\Client;
use App\Models\ClientBill;
use App\Models\ClientGuardAttendance;
use App\Models\ClientPayment;
use App\Models\SecurityGuard;
use App\Services\PermissionService;
use App\Support\TenantContext;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;
use Symfony\Component\HttpFoundation\Response;

#[Title('Dashboard')]
class Dashboard extends Component
{
    protected TenantContext $tenant;

    protected PermissionService $permissions;

    public function boot(TenantContext $tenant, PermissionService $permissions): void
    {
        $this->tenant = $tenant;
        $this->permissions = $permissions;
    }

    public function render(): View
    {
        abort_unless($this->permissions->can($this->tenant->user(), 'view_dashboard'), Response::HTTP_FORBIDDEN);

        $business = $this->tenant->business();
        $businessId = $business->getKey();

        return view('livewire.dashboard', [
            'business' => $business,
            'metrics' => [
                'clients' => Client::query()->where('businessId', $businessId)->count(),
                'guards' => SecurityGuard::query()->where('businessId', $businessId)->count(),
                'attendance' => ClientGuardAttendance::query()->where('businessId', $businessId)->count(),
                'billed' => ClientBill::query()->where('businessId', $businessId)->sum('total'),
                'paid' => ClientPayment::query()->where('businessId', $businessId)->sum('amount'),
            ],
        ]);
    }
}
