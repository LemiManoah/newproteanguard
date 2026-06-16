<?php

namespace App\Livewire\Foundation;

use App\Models\PaymentMode;
use App\Services\AuditService;
use App\Services\PermissionService;
use App\Support\TenantContext;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;
use Symfony\Component\HttpFoundation\Response;

#[Title('Payment Modes')]
class PaymentModesPage extends Component
{
    protected TenantContext $tenant;

    protected PermissionService $permissions;

    protected AuditService $audit;

    public function boot(TenantContext $tenant, PermissionService $permissions, AuditService $audit): void
    {
        $this->tenant = $tenant;
        $this->permissions = $permissions;
        $this->audit = $audit;
    }

    public function setDefault(int $paymentModeId): void
    {
        abort_unless($this->permissions->can($this->tenant->user(), 'edit_paymodes'), Response::HTTP_FORBIDDEN);

        DB::transaction(function () use ($paymentModeId): void {
            $paymentMode = PaymentMode::query()
                ->where('businessId', $this->tenant->businessId())
                ->findOrFail($paymentModeId);

            PaymentMode::query()
                ->where('businessId', $this->tenant->businessId())
                ->update(['is_default' => false]);

            $paymentMode->forceFill(['is_default' => true])->save();

            $this->audit->record("Set default payment mode to {$paymentMode->name}", $this->tenant->user());
        });
    }

    public function render(): View
    {
        abort_unless($this->permissions->can($this->tenant->user(), 'view_paymodes'), Response::HTTP_FORBIDDEN);

        return view('livewire.foundation.payment-modes-page', [
            'canEdit' => $this->permissions->can($this->tenant->user(), 'edit_paymodes'),
            'paymentModes' => PaymentMode::query()
                ->where('businessId', $this->tenant->businessId())
                ->latest()
                ->get(),
        ]);
    }
}
