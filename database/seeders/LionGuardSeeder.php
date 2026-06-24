<?php

namespace Database\Seeders;

use App\Enums\AttendanceStatus;
use App\Enums\AvailabilityStatus;
use App\Enums\BillingCycle;
use App\Enums\BulletMovementType;
use App\Enums\CoaCategoryName;
use App\Enums\CoaCategoryType;
use App\Enums\DebitCredit;
use App\Enums\GuardGender;
use App\Enums\GunOwnerType;
use App\Enums\InventoryMovementType;
use App\Enums\MaritalStatus;
use App\Enums\PaymentModeType;
use App\Enums\PayrollCategory;
use App\Enums\PayrollStatus;
use App\Enums\SalaryPaymentChannel;
use App\Enums\ScheduleType;
use App\Models\Advance;
use App\Models\BulletAddition;
use App\Models\BulletMovement;
use App\Models\BulletUsage;
use App\Models\Business;
use App\Models\ChartOfAccount;
use App\Models\Client;
use App\Models\ClientBill;
use App\Models\ClientCategory;
use App\Models\ClientDocument;
use App\Models\ClientGuard;
use App\Models\ClientGuardAttendance;
use App\Models\ClientPayment;
use App\Models\CoaCategory;
use App\Models\CoaTransaction;
use App\Models\Country;
use App\Models\Expense;
use App\Models\ExpenseBudget;
use App\Models\ExpenseCategory;
use App\Models\FinancialYear;
use App\Models\GuardDocument;
use App\Models\GuardReferee;
use App\Models\Gun;
use App\Models\GunAssignment;
use App\Models\GunMaintenance;
use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\InventoryMovement;
use App\Models\InventoryStockin;
use App\Models\InventoryStockUsage;
use App\Models\PaymentMode;
use App\Models\Payroll;
use App\Models\Permission;
use App\Models\Reconciliation;
use App\Models\SalaryAddition;
use App\Models\SalaryCategory;
use App\Models\SalaryDeduction;
use App\Models\SalaryPayment;
use App\Models\SalaryPaymentLedger;
use App\Models\SecurityGuard;
use App\Models\SentMessage;
use App\Models\SmsBalance;
use App\Models\Staff;
use App\Models\StaffPosition;
use App\Models\StandardCharge;
use App\Models\StockInCart;
use App\Models\StockInDetail;
use App\Models\TempSalary;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class LionGuardSeeder extends Seeder
{
    public function run(): void
    {
        $today = '2026-06-16';

        $country = $this->seedModel(Country::class, [
            'country_code' => 'UG',
        ], [
            'country_name' => 'Uganda',
            'dial_code' => '+256',
            'currency_code' => 'UGX',
            'currency' => 'Uganda Shilling',
            'capital_city' => 'Kampala',
            'country_dormain' => 'ug',
        ]);

        $business = $this->seedModel(Business::class, [
            'name' => 'LionGuard',
        ], [
            'contact1' => '0772000001',
            'contact2' => '0772000002',
            'email' => 'admin@lionguard.test',
            'location' => 'Kampala',
            'website' => 'https://lionguard.test',
            'tin' => '1000000001',
            'guard_duty' => 30000,
            'guard_overtime' => 5000,
            'savings' => 10000,
            'payroll_start' => 1,
            'payroll_end' => 30,
            'currency' => $country->getKey(),
            'status' => true,
        ]);

        $adminRole = $this->seedModel(Permission::class, [
            'businessId' => $business->getKey(),
            'name' => 'Administrator',
        ], array_merge([
            'status' => true,
        ], array_fill_keys(Permission::PERMISSION_COLUMNS, true)));

        $admin = $this->seedModel(User::class, [
            'email' => 'admin@lionguard.test',
        ], [
            'name' => 'LionGuard Admin',
            'username' => 'lionadmin',
            'contact' => '0772000001',
            'password' => Hash::make('password'),
            'email_verified_at' => $today,
            'roleId' => $adminRole->getKey(),
            'businessId' => $business->getKey(),
            'status' => true,
        ]);

        $adminRole->forceFill(['userId' => $admin->getKey()])->save();

        $financialYear = $this->seedModel(FinancialYear::class, [
            'businessId' => $business->getKey(),
            'name' => 'FY 2026',
        ], [
            'start' => '2026-01-01',
            'end' => '2026-12-31',
            'status' => true,
            'Active' => true,
            'userId' => $admin->getKey(),
        ]);

        $cashMode = $this->seedModel(PaymentMode::class, [
            'businessId' => $business->getKey(),
            'name' => 'Main Cash',
        ], [
            'type' => PaymentModeType::Cash->value,
            'type_name' => 'Cash Office',
            'account' => 'CASH-001',
            'opening_balance' => 500000,
            'status' => true,
            'is_default' => true,
            'userId' => $admin->getKey(),
        ]);

        $bankMode = $this->seedModel(PaymentMode::class, [
            'businessId' => $business->getKey(),
            'name' => 'Stanbic Bank',
        ], [
            'type' => PaymentModeType::Bank->value,
            'type_name' => 'Stanbic',
            'account' => '903000001',
            'opening_balance' => 2500000,
            'status' => true,
            'is_default' => false,
            'userId' => $admin->getKey(),
        ]);

        $mobileMode = $this->seedModel(PaymentMode::class, [
            'businessId' => $business->getKey(),
            'name' => 'MTN Mobile Money',
        ], [
            'type' => PaymentModeType::Mobile->value,
            'type_name' => 'MTN',
            'account' => '0772000001',
            'opening_balance' => 300000,
            'status' => true,
            'is_default' => false,
            'userId' => $admin->getKey(),
        ]);

        $coaCategories = $this->seedCoaCategories($business, $admin);
        $accounts = $this->seedChartOfAccounts($business, $admin, $coaCategories);

        $clientCategory = $this->seedModel(ClientCategory::class, [
            'businessId' => $business->getKey(),
            'name' => 'Corporate',
        ], [
            'status' => true,
            'userId' => $admin->getKey(),
        ]);

        $client = $this->seedModel(Client::class, [
            'businessId' => $business->getKey(),
            'name' => 'Acacia Mall',
        ], [
            'categoryId' => $clientCategory->getKey(),
            'contact1' => '0755000001',
            'contact2' => '0755000002',
            'email' => 'security@acacia.test',
            'id_no' => 'CL-001',
            'tin' => 'TIN-ACACIA',
            'vat_no' => 'VAT-ACACIA',
            'address' => 'Kisementi, Kampala',
            'billing_cycle' => BillingCycle::Monthly->value,
            'amount' => 1800000,
            'no_guards' => 2,
            'actual_guards' => 2,
            'bill_start' => '2026-06-01',
            'schedule_type' => ScheduleType::FullTime->value,
            'assigned' => true,
            'status' => true,
            'userId' => $admin->getKey(),
        ]);

        $client2 = $this->seedModel(Client::class, [
            'businessId' => $business->getKey(),
            'name' => 'Nakasero Hospital',
        ], [
            'categoryId' => $clientCategory->getKey(),
            'contact1' => '0755000011',
            'contact2' => '0755000012',
            'email' => 'security@nakasero.test',
            'id_no' => 'CL-002',
            'tin' => 'TIN-NAKASERO',
            'vat_no' => 'VAT-NAKASERO',
            'address' => 'Nakasero, Kampala',
            'billing_cycle' => BillingCycle::Monthly->value,
            'amount' => 2200000,
            'no_guards' => 2,
            'actual_guards' => 2,
            'bill_start' => '2026-06-01',
            'schedule_type' => ScheduleType::FullTime->value,
            'assigned' => true,
            'status' => true,
            'userId' => $admin->getKey(),
        ]);

        $guard = $this->seedModel(SecurityGuard::class, [
            'businessId' => $business->getKey(),
            'code' => 'LG-001',
        ], [
            'code_number' => 1,
            'fname' => 'Daniel',
            'lname' => 'Okello',
            'contact1' => '0701000001',
            'contact2' => '0701000002',
            'email' => 'daniel.okello@lionguard.test',
            'dob' => '1994-03-12',
            'weight' => 78,
            'height' => 1.78,
            'join_date' => '2026-01-10',
            'gender' => GuardGender::Male->value,
            'nationality' => 'Ugandan',
            'religion' => 'Christian',
            'tribe' => 'Acholi',
            'marital_status' => MaritalStatus::Married->value,
            'address' => 'Ntinda',
            'home_contact' => '0701000003',
            'home_location' => 'Gulu',
            'father_name' => 'Peter Okello',
            'mother_name' => 'Grace Okello',
            'nok' => 'Sarah Okello',
            'nok_contact' => '0701000004',
            'nok_relationship' => 'Spouse',
            'nok_residence' => 'Ntinda',
            'id_no' => 'CM94000001A',
            'id_expiry' => '2030-12-31',
            'languages' => 'English, Luo, Luganda',
            'assigned' => true,
            'doc_verified' => true,
            'status' => true,
            'userId' => $admin->getKey(),
        ]);

        $guard2 = $this->seedModel(SecurityGuard::class, [
            'businessId' => $business->getKey(),
            'code' => 'LG-002',
        ], [
            'code_number' => 2,
            'fname' => 'Aisha',
            'lname' => 'Nabukeera',
            'contact1' => '0701000011',
            'contact2' => '0701000012',
            'email' => 'aisha.nabukeera@lionguard.test',
            'dob' => '1997-08-24',
            'weight' => 64,
            'height' => 1.65,
            'join_date' => '2026-02-03',
            'gender' => GuardGender::Female->value,
            'nationality' => 'Ugandan',
            'religion' => 'Muslim',
            'tribe' => 'Ganda',
            'marital_status' => MaritalStatus::Single->value,
            'address' => 'Kawempe',
            'home_contact' => '0701000013',
            'home_location' => 'Masaka',
            'father_name' => 'Hassan Ssemanda',
            'mother_name' => 'Rehema Ssemanda',
            'nok' => 'Mariam Ssemanda',
            'nok_contact' => '0701000014',
            'nok_relationship' => 'Sister',
            'nok_residence' => 'Kawempe',
            'id_no' => 'CF97000002B',
            'id_expiry' => '2031-08-31',
            'languages' => 'English, Luganda',
            'assigned' => true,
            'doc_verified' => true,
            'status' => true,
            'userId' => $admin->getKey(),
        ]);

        $guard3 = $this->seedModel(SecurityGuard::class, [
            'businessId' => $business->getKey(),
            'code' => 'LG-003',
        ], [
            'code_number' => 3,
            'fname' => 'Michael',
            'lname' => 'Otim',
            'contact1' => '0701000021',
            'contact2' => '0701000022',
            'email' => 'michael.otim@lionguard.test',
            'dob' => '1992-11-05',
            'weight' => 82,
            'height' => 1.82,
            'join_date' => '2026-03-15',
            'gender' => GuardGender::Male->value,
            'nationality' => 'Ugandan',
            'religion' => 'Christian',
            'tribe' => 'Iteso',
            'marital_status' => MaritalStatus::Married->value,
            'address' => 'Kireka',
            'home_contact' => '0701000023',
            'home_location' => 'Soroti',
            'father_name' => 'Samuel Otim',
            'mother_name' => 'Agnes Otim',
            'nok' => 'Faith Otim',
            'nok_contact' => '0701000024',
            'nok_relationship' => 'Spouse',
            'nok_residence' => 'Kireka',
            'id_no' => 'CM92000003C',
            'id_expiry' => '2030-10-31',
            'languages' => 'English, Ateso, Luganda',
            'assigned' => true,
            'doc_verified' => true,
            'status' => true,
            'userId' => $admin->getKey(),
        ]);

        $guard4 = $this->seedModel(SecurityGuard::class, [
            'businessId' => $business->getKey(),
            'code' => 'LG-004',
        ], [
            'code_number' => 4,
            'fname' => 'Joseph',
            'lname' => 'Mugerwa',
            'contact1' => '0701000031',
            'contact2' => '0701000032',
            'email' => 'joseph.mugerwa@lionguard.test',
            'dob' => '1995-01-19',
            'weight' => 75,
            'height' => 1.74,
            'join_date' => '2026-04-20',
            'gender' => GuardGender::Male->value,
            'nationality' => 'Ugandan',
            'religion' => 'Christian',
            'tribe' => 'Ganda',
            'marital_status' => MaritalStatus::Single->value,
            'address' => 'Makindye',
            'home_contact' => '0701000033',
            'home_location' => 'Mukono',
            'father_name' => 'Paul Mugerwa',
            'mother_name' => 'Rose Mugerwa',
            'nok' => 'Allen Mugerwa',
            'nok_contact' => '0701000034',
            'nok_relationship' => 'Mother',
            'nok_residence' => 'Mukono',
            'id_no' => 'CM95000004D',
            'id_expiry' => '2032-01-31',
            'languages' => 'English, Luganda',
            'assigned' => true,
            'doc_verified' => true,
            'status' => true,
            'userId' => $admin->getKey(),
        ]);

        $deployment = $this->seedModel(ClientGuard::class, [
            'businessId' => $business->getKey(),
            'clientId' => $client->getKey(),
            'guardId' => $guard->getKey(),
        ], [
            'from' => '2026-06-01',
            'to' => null,
            'schedule_type' => ScheduleType::FullTime->value,
            'status' => true,
            'userId' => $admin->getKey(),
        ]);

        $this->seedModel(ClientGuard::class, [
            'businessId' => $business->getKey(),
            'clientId' => $client->getKey(),
            'guardId' => $guard2->getKey(),
        ], [
            'from' => '2026-06-01',
            'to' => null,
            'schedule_type' => ScheduleType::FullTime->value,
            'status' => true,
            'userId' => $admin->getKey(),
        ]);

        $this->seedModel(ClientGuard::class, [
            'businessId' => $business->getKey(),
            'clientId' => $client2->getKey(),
            'guardId' => $guard3->getKey(),
        ], [
            'from' => '2026-06-01',
            'to' => null,
            'schedule_type' => ScheduleType::Day->value,
            'status' => true,
            'userId' => $admin->getKey(),
        ]);

        $this->seedModel(ClientGuard::class, [
            'businessId' => $business->getKey(),
            'clientId' => $client2->getKey(),
            'guardId' => $guard4->getKey(),
        ], [
            'from' => '2026-06-01',
            'to' => null,
            'schedule_type' => ScheduleType::Night->value,
            'status' => true,
            'userId' => $admin->getKey(),
        ]);

        $this->seedModel(ClientGuardAttendance::class, [
            'businessId' => $business->getKey(),
            'deploymentId' => $deployment->getKey(),
            'date' => '2026-06-16',
        ], [
            'clientId' => $client->getKey(),
            'guardId' => $guard->getKey(),
            'schedule_type' => ScheduleType::FullTime->value,
            'attended' => AttendanceStatus::Present->value,
            'reason' => 'Demo attendance',
            'userId' => $admin->getKey(),
        ]);

        $this->seedModel(ClientDocument::class, [
            'businessId' => $business->getKey(),
            'clientId' => $client->getKey(),
            'title' => 'Service Agreement',
        ], [
            'type' => 3,
            'disk' => 'client_documents',
            'path' => 'demo/acacia-service-agreement.pdf',
            'original_name' => 'acacia-service-agreement.pdf',
            'status' => true,
            'userId' => $admin->getKey(),
        ]);

        $this->seedModel(GuardDocument::class, [
            'businessId' => $business->getKey(),
            'guardId' => $guard->getKey(),
            'title' => 'National ID',
        ], [
            'type' => 1,
            'disk' => 'guard_documents',
            'path' => 'demo/daniel-okello-national-id.pdf',
            'original_name' => 'daniel-okello-national-id.pdf',
            'status' => true,
            'userId' => $admin->getKey(),
        ]);

        $this->seedModel(GuardReferee::class, [
            'businessId' => $business->getKey(),
            'guardId' => $guard->getKey(),
            'name' => 'Moses Akena',
        ], [
            'contact' => '0702000001',
            'residence' => 'Gulu',
            'status' => true,
            'userId' => $admin->getKey(),
        ]);

        $expenseCategory = $this->seedModel(ExpenseCategory::class, [
            'businessId' => $business->getKey(),
            'name' => 'Transport',
        ], [
            'status' => true,
            'userId' => $admin->getKey(),
        ]);

        $this->seedModel(ExpenseBudget::class, [
            'businessId' => $business->getKey(),
            'categoryId' => $expenseCategory->getKey(),
            'yearId' => $financialYear->getKey(),
        ], [
            'amount' => 12000000,
            'status' => true,
            'userId' => $admin->getKey(),
        ]);

        $expense = $this->seedModel(Expense::class, [
            'businessId' => $business->getKey(),
            'categoryId' => $expenseCategory->getKey(),
            'date' => '2026-06-16',
        ], [
            'amount' => 75000,
            'modeId' => $cashMode->getKey(),
            'yearId' => $financialYear->getKey(),
            'description' => 'Fuel for patrol route',
            'userId' => $admin->getKey(),
        ]);

        $bill = $this->seedModel(ClientBill::class, [
            'businessId' => $business->getKey(),
            'invoice_number' => 1001,
        ], [
            'invoice' => 1,
            'clientId' => $client->getKey(),
            'bill_cycle' => BillingCycle::Monthly->value,
            'cycles' => 1,
            'amount' => 1800000,
            'total' => 1800000,
            'start_date' => '2026-06-01',
            'date' => '2026-06-16',
            'end_date' => '2026-06-30',
            'status' => true,
            'userId' => $admin->getKey(),
        ]);

        $payment = $this->seedModel(ClientPayment::class, [
            'businessId' => $business->getKey(),
            'receipt_number' => 1001,
        ], [
            'receipt' => 1,
            'clientId' => $client->getKey(),
            'modeId' => $bankMode->getKey(),
            'amount' => 900000,
            'payment_date' => '2026-06-16',
            'ref_number' => 'BNK-LG-1001',
            'description' => 'Partial payment for June invoice',
            'status' => true,
            'userId' => $admin->getKey(),
        ]);

        $this->seedFinancialTransactions($business, $admin, $accounts, $bill, $payment, $expense, $client);
        $this->seedAssets($business, $admin, $guard);
        $this->seedInventory($business, $admin, $cashMode, $guard);
        $this->seedPayroll($business, $admin, $cashMode, $guard, $today);

        $this->seedModel(SmsBalance::class, [
            'businessId' => $business->getKey(),
        ], [
            'qty' => 250,
        ]);

        $this->seedModel(SentMessage::class, [
            'businessId' => $business->getKey(),
            'message_id' => 'DEMO-SMS-001',
        ], [
            'sender' => $admin->getKey(),
            'receiver' => '256755000001',
            'message_type' => 'demo',
            'message' => 'LionGuard demo SMS message.',
            'size' => 1,
        ]);
    }

    /**
     * @return array<string, CoaCategory>
     */
    private function seedCoaCategories(Business $business, User $admin): array
    {
        $rows = [
            [CoaCategoryName::Cash, CoaCategoryType::Assets, DebitCredit::Debit, 100],
            [CoaCategoryName::Bank, CoaCategoryType::Assets, DebitCredit::Debit, 110],
            [CoaCategoryName::AccountsReceivable, CoaCategoryType::Assets, DebitCredit::Debit, 120],
            [CoaCategoryName::AccountsPayable, CoaCategoryType::Liability, DebitCredit::Credit, 200],
            [CoaCategoryName::ClientPayments, CoaCategoryType::Revenues, DebitCredit::Credit, 400],
            [CoaCategoryName::OtherRevenue, CoaCategoryType::Revenues, DebitCredit::Credit, 410],
            [CoaCategoryName::PhysicalAssets, CoaCategoryType::Assets, DebitCredit::Debit, 150],
            [CoaCategoryName::OwnersEquity, CoaCategoryType::Equity, DebitCredit::Credit, 300],
            [CoaCategoryName::Drawings, CoaCategoryType::Drawings, DebitCredit::Debit, 310],
        ];

        $categories = [];

        foreach ($rows as [$name, $type, $drCr, $code]) {
            $categories[$name->value] = $this->seedModel(CoaCategory::class, [
                'businessId' => $business->getKey(),
                'name' => $name->value,
            ], [
                'type' => $type->value,
                'dr_cr' => $drCr->value,
                'code_number' => $code,
                'userId' => $admin->getKey(),
            ]);
        }

        return $categories;
    }

    /**
     * @param  array<string, CoaCategory>  $categories
     * @return array<string, ChartOfAccount>
     */
    private function seedChartOfAccounts(Business $business, User $admin, array $categories): array
    {
        $rows = [
            'cash' => ['Main Cash Account', CoaCategoryName::Cash->value, 1001, 1],
            'bank' => ['Main Bank Account', CoaCategoryName::Bank->value, 1101, 2],
            'receivable' => ['Client Receivables', CoaCategoryName::AccountsReceivable->value, 1201, 3],
            'payable' => ['Payroll Payables', CoaCategoryName::AccountsPayable->value, 2001, 4],
            'revenue' => ['Guarding Service Revenue', CoaCategoryName::ClientPayments->value, 4001, 5],
            'expense' => ['Transport Expense', CoaCategoryName::Drawings->value, 3101, 6],
            'inventory' => ['Uniform Inventory', CoaCategoryName::PhysicalAssets->value, 1501, 7],
        ];

        $accounts = [];

        foreach ($rows as $key => [$name, $categoryName, $codeNumber, $code]) {
            $accounts[$key] = $this->seedModel(ChartOfAccount::class, [
                'businessId' => $business->getKey(),
                'name' => $name,
            ], [
                'categoryId' => $categories[$categoryName]->getKey(),
                'code_number' => $codeNumber,
                'code' => $code,
                'status' => true,
                'userId' => $admin->getKey(),
            ]);
        }

        return $accounts;
    }

    /**
     * @param  array<string, ChartOfAccount>  $accounts
     */
    private function seedFinancialTransactions(
        Business $business,
        User $admin,
        array $accounts,
        ClientBill $bill,
        ClientPayment $payment,
        Expense $expense,
        Client $client
    ): void {
        $this->seedModel(CoaTransaction::class, [
            'businessId' => $business->getKey(),
            'ref_no' => 'INV-1001-DR',
        ], [
            'date' => '2026-06-16',
            'coa' => $accounts['receivable']->getKey(),
            'clientId' => $client->getKey(),
            'dr_amount' => 1800000,
            'cr_amount' => 0,
            'description' => 'June guarding invoice',
            'type' => 'client_bill',
            'txnId' => $bill->getKey(),
            'userId' => $admin->getKey(),
        ]);

        $this->seedModel(CoaTransaction::class, [
            'businessId' => $business->getKey(),
            'ref_no' => 'INV-1001-CR',
        ], [
            'date' => '2026-06-16',
            'coa' => $accounts['revenue']->getKey(),
            'clientId' => $client->getKey(),
            'dr_amount' => 0,
            'cr_amount' => 1800000,
            'description' => 'June guarding invoice revenue',
            'type' => 'client_bill',
            'txnId' => $bill->getKey(),
            'userId' => $admin->getKey(),
        ]);

        $this->seedModel(CoaTransaction::class, [
            'businessId' => $business->getKey(),
            'ref_no' => 'RCT-1001-DR',
        ], [
            'date' => '2026-06-16',
            'coa' => $accounts['bank']->getKey(),
            'clientId' => $client->getKey(),
            'dr_amount' => 900000,
            'cr_amount' => 0,
            'description' => 'Client payment received',
            'type' => 'client_payment',
            'txnId' => $payment->getKey(),
            'userId' => $admin->getKey(),
        ]);

        $this->seedModel(CoaTransaction::class, [
            'businessId' => $business->getKey(),
            'ref_no' => 'RCT-1001-CR',
        ], [
            'date' => '2026-06-16',
            'coa' => $accounts['receivable']->getKey(),
            'clientId' => $client->getKey(),
            'dr_amount' => 0,
            'cr_amount' => 900000,
            'description' => 'Receivable reduced by client payment',
            'type' => 'client_payment',
            'txnId' => $payment->getKey(),
            'userId' => $admin->getKey(),
        ]);

        $this->seedModel(CoaTransaction::class, [
            'businessId' => $business->getKey(),
            'ref_no' => 'EXP-1001-DR',
        ], [
            'date' => '2026-06-16',
            'coa' => $accounts['expense']->getKey(),
            'dr_amount' => 75000,
            'cr_amount' => 0,
            'description' => 'Transport expense',
            'type' => 'expense',
            'txnId' => $expense->getKey(),
            'userId' => $admin->getKey(),
        ]);

        $this->seedModel(CoaTransaction::class, [
            'businessId' => $business->getKey(),
            'ref_no' => 'EXP-1001-CR',
        ], [
            'date' => '2026-06-16',
            'coa' => $accounts['cash']->getKey(),
            'dr_amount' => 0,
            'cr_amount' => 75000,
            'description' => 'Cash paid for transport expense',
            'type' => 'expense',
            'txnId' => $expense->getKey(),
            'userId' => $admin->getKey(),
        ]);
    }

    private function seedAssets(Business $business, User $admin, SecurityGuard $guard): void
    {
        $gun = $this->seedModel(Gun::class, [
            'businessId' => $business->getKey(),
            'serial_number' => 'LG-GUN-001',
        ], [
            'type' => 'Shotgun',
            'mark_number' => 'MK-001',
            'bullets' => 25,
            'owner' => GunOwnerType::Owned->value,
            'available' => AvailabilityStatus::Unavailable->value,
            'status' => true,
            'userId' => $admin->getKey(),
        ]);

        $this->seedModel(GunAssignment::class, [
            'businessId' => $business->getKey(),
            'gunId' => $gun->getKey(),
            'guardId' => $guard->getKey(),
        ], [
            'start_date' => '2026-06-01',
            'description' => 'Demo active assignment',
            'status' => true,
            'userId' => $admin->getKey(),
        ]);

        $addition = $this->seedModel(BulletAddition::class, [
            'businessId' => $business->getKey(),
            'gunId' => $gun->getKey(),
            'date' => '2026-06-01',
        ], [
            'quantity' => 25,
            'brought_by' => 'Armory',
            'description' => 'Opening rounds',
            'userId' => $admin->getKey(),
        ]);

        $usage = $this->seedModel(BulletUsage::class, [
            'businessId' => $business->getKey(),
            'gunId' => $gun->getKey(),
            'date' => '2026-06-16',
        ], [
            'guardId' => $guard->getKey(),
            'quantity' => 2,
            'description' => 'Range test',
            'userId' => $admin->getKey(),
        ]);

        $this->seedModel(BulletMovement::class, [
            'businessId' => $business->getKey(),
            'gunId' => $gun->getKey(),
            'type' => BulletMovementType::Addition->value,
            'tid' => $addition->getKey(),
        ], [
            'date' => '2026-06-01',
            'quantity_in' => 25,
            'quantity_out' => 0,
            'description' => 'Opening bullet balance',
            'userId' => $admin->getKey(),
        ]);

        $this->seedModel(BulletMovement::class, [
            'businessId' => $business->getKey(),
            'gunId' => $gun->getKey(),
            'type' => BulletMovementType::Usage->value,
            'tid' => $usage->getKey(),
        ], [
            'date' => '2026-06-16',
            'quantity_in' => 0,
            'quantity_out' => 2,
            'description' => 'Bullet usage',
            'userId' => $admin->getKey(),
        ]);

        $this->seedModel(GunMaintenance::class, [
            'businessId' => $business->getKey(),
            'gunId' => $gun->getKey(),
            'date' => '2026-06-10',
        ], [
            'work_by' => 'Armory Technician',
            'description' => 'Routine cleaning',
            'userId' => $admin->getKey(),
        ]);
    }

    private function seedInventory(Business $business, User $admin, PaymentMode $cashMode, SecurityGuard $guard): void
    {
        $unit = $this->seedModel(Unit::class, [
            'businessId' => $business->getKey(),
            'symbol' => 'pcs',
        ], [
            'status' => true,
            'userId' => $admin->getKey(),
        ]);

        $category = $this->seedModel(InventoryCategory::class, [
            'businessId' => $business->getKey(),
            'name' => 'Uniforms',
        ], [
            'status' => true,
            'userId' => $admin->getKey(),
        ]);

        $item = $this->seedModel(InventoryItem::class, [
            'businessId' => $business->getKey(),
            'name' => 'Security Shirt',
        ], [
            'categoryId' => $category->getKey(),
            'unitId' => $unit->getKey(),
            'quantity' => 20,
            'opening_stock' => 20,
            'buying_price' => 35000,
            'last_buying_price' => 35000,
            'status' => true,
            'userId' => $admin->getKey(),
        ]);

        $stockIn = $this->seedModel(InventoryStockin::class, [
            'businessId' => $business->getKey(),
            'date' => '2026-06-05',
            'supplierId' => 0,
        ], [
            'total' => 700000,
            'paid' => 700000,
            'paymentMode' => $cashMode->getKey(),
            'userId' => $admin->getKey(),
        ]);

        $this->seedModel(StockInDetail::class, [
            'businessId' => $business->getKey(),
            'stockInId' => $stockIn->getKey(),
            'itemId' => $item->getKey(),
        ], [
            'quantity' => 20,
            'buying_price' => 35000,
            'userId' => $admin->getKey(),
        ]);

        $this->seedModel(StockInCart::class, [
            'businessId' => $business->getKey(),
            'itemId' => $item->getKey(),
        ], [
            'quantity' => 5,
            'buying_price' => 35000,
            'userId' => $admin->getKey(),
        ]);

        $usage = $this->seedModel(InventoryStockUsage::class, [
            'businessId' => $business->getKey(),
            'itemId' => $item->getKey(),
            'date' => '2026-06-16',
        ], [
            'guardId' => $guard->getKey(),
            'quantity' => 1,
            'description' => 'Issued to guard',
            'userId' => $admin->getKey(),
        ]);

        $this->seedModel(InventoryMovement::class, [
            'businessId' => $business->getKey(),
            'itemId' => $item->getKey(),
            'type' => InventoryMovementType::Stocking->value,
            'tid' => $stockIn->getKey(),
        ], [
            'date' => '2026-06-05',
            'quantity_in' => 20,
            'quantity_out' => 0,
            'description' => 'Stock in',
            'userId' => $admin->getKey(),
        ]);

        $this->seedModel(InventoryMovement::class, [
            'businessId' => $business->getKey(),
            'itemId' => $item->getKey(),
            'type' => InventoryMovementType::Usage->value,
            'tid' => $usage->getKey(),
        ], [
            'date' => '2026-06-16',
            'quantity_in' => 0,
            'quantity_out' => 1,
            'description' => 'Issued stock',
            'userId' => $admin->getKey(),
        ]);

        $this->seedModel(Reconciliation::class, [
            'businessId' => $business->getKey(),
            'itemId' => $item->getKey(),
            'date' => '2026-06-16',
        ], [
            'from' => 20,
            'to' => 19,
            'userId' => $admin->getKey(),
        ]);
    }

    private function seedPayroll(Business $business, User $admin, PaymentMode $cashMode, SecurityGuard $guard, string $today): void
    {
        $position = $this->seedModel(StaffPosition::class, [
            'businessId' => $business->getKey(),
            'name' => 'Guard',
        ], [
            'status' => true,
            'userId' => $admin->getKey(),
        ]);

        $salaryCategory = $this->seedModel(SalaryCategory::class, [
            'businessId' => $business->getKey(),
            'name' => 'Standard Guard Daily',
        ], [
            'daily_amount' => 30000,
            'status' => true,
            'userId' => $admin->getKey(),
        ]);

        $staff = $this->seedModel(Staff::class, [
            'businessId' => $business->getKey(),
            'email' => 'daniel.payroll@lionguard.test',
        ], [
            'name' => 'Daniel Okello',
            'gender' => GuardGender::Male->value,
            'contact1' => '0701000001',
            'nin' => 'CM94000001A',
            'address' => 'Ntinda',
            'dob' => '1994-03-12',
            'positionId' => $position->getKey(),
            'guardId' => $guard->getKey(),
            'salaryCategoryId' => $salaryCategory->getKey(),
            'salary' => 900000,
            'dop' => '2026-06-01',
            'status' => true,
            'userId' => $admin->getKey(),
        ]);

        $payroll = $this->seedModel(Payroll::class, [
            'businessId' => $business->getKey(),
            'month' => 6,
            'year' => 2026,
            'category' => PayrollCategory::All->value,
        ], [
            'review_date' => $today,
            'approval_date' => null,
            'review_comment' => 'Demo payroll reviewed',
            'approval_comment' => null,
            'status' => PayrollStatus::Reviewed->value,
            'guard_overtime' => 5000,
            'savings' => 10000,
            'userId' => $admin->getKey(),
        ]);

        $salaryPayment = $this->seedModel(SalaryPayment::class, [
            'businessId' => $business->getKey(),
            'staffId' => $staff->getKey(),
            'month' => 6,
            'year' => 2026,
        ], [
            'payrollId' => $payroll->getKey(),
            'salary' => 900000,
            'overtime_amount' => 50000,
            'savings' => 10000,
            'days_worked' => 30,
            'overtime_worked' => 10,
            'userId' => $admin->getKey(),
        ]);

        $this->seedModel(SalaryPaymentLedger::class, [
            'businessId' => $business->getKey(),
            'salaryId' => $salaryPayment->getKey(),
            'date' => '2026-06-30',
        ], [
            'staffId' => $staff->getKey(),
            'amount' => 940000,
            'mode' => $cashMode->getKey(),
            'channel' => SalaryPaymentChannel::Default->value,
            'userId' => $admin->getKey(),
        ]);

        $this->seedModel(SalaryAddition::class, [
            'businessId' => $business->getKey(),
            'staffId' => $staff->getKey(),
            'month' => 6,
            'year' => 2026,
            'description' => 'Night allowance',
        ], [
            'amount' => 50000,
            'userId' => $admin->getKey(),
        ]);

        $this->seedModel(SalaryDeduction::class, [
            'businessId' => $business->getKey(),
            'staffId' => $staff->getKey(),
            'month' => 6,
            'year' => 2026,
            'description' => 'Uniform deduction',
        ], [
            'amount' => 25000,
            'userId' => $admin->getKey(),
        ]);

        $this->seedModel(Advance::class, [
            'businessId' => $business->getKey(),
            'staffId' => $staff->getKey(),
            'date' => '2026-06-12',
        ], [
            'amount' => 100000,
            'deductMonth' => 6,
            'deductYear' => 2026,
            'description' => 'Demo salary advance',
            'mode' => $cashMode->getKey(),
            'userId' => $admin->getKey(),
        ]);

        $this->seedModel(StandardCharge::class, [
            'businessId' => $business->getKey(),
            'staffId' => $staff->getKey(),
            'month' => 6,
            'year' => 2026,
        ], [
            'mma' => 10000,
            'rent' => 0,
            'uniform' => 25000,
            'payee' => 0,
            'nssf' => 45000,
            'userId' => $admin->getKey(),
        ]);

        $this->seedModel(TempSalary::class, [
            'businessId' => $business->getKey(),
            'staffId' => $staff->getKey(),
            'salaryId' => $salaryPayment->getKey(),
        ], [
            'balance' => 0,
            'amount' => 940000,
            'percent' => 100,
            'userId' => $admin->getKey(),
        ]);
    }

    /**
     * @template TModel of Model
     *
     * @param  class-string<TModel>  $class
     * @param  array<string, mixed>  $where
     * @param  array<string, mixed>  $values
     * @return TModel
     */
    private function seedModel(string $class, array $where, array $values): Model
    {
        $model = $class::query()->where($where)->first() ?? new $class;
        $model->forceFill($where + $values)->save();

        return $model;
    }
}
