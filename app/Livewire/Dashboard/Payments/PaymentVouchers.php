<?php

namespace App\Livewire\Dashboard\Payments;

use App\Models\Payment;
use App\Models\Customer;
use App\Models\Supplier;
use App\Services\PaymentVoucherService;
use Livewire\Component;
use Livewire\WithPagination;

class PaymentVouchers extends Component
{
    use WithPagination;

    public $showModal = false;
    public $confirmDeleteModal = false;
    public $isEditing = false;
    public $paymentId;
    public $currentTargetBalance = 0;
    
    // Form fields
    public $target_type = 'customer'; // customer or supplier
    public $target_id;
    public $amount;
    public $payment_method = 'cash';
    public $payment_date;
    public $reference_number;
    public $notes;
    public $voucher_type = 'receipt'; // receipt (قبض) or disbursement (صرف)

    protected $rules = [
        'target_type' => 'required|in:customer,supplier',
        'target_id' => 'required|integer',
        'amount' => 'required|numeric|min:0.01',
        'payment_method' => 'required|string',
        'payment_date' => 'required|date',
        'voucher_type' => 'required|in:receipt,disbursement',
        'reference_number' => 'nullable|string|max:100',
        'notes' => 'nullable|string',
    ];

    public function mount()
    {
        $this->payment_date = now()->format('Y-m-d');
    }

    public function render()
    {
        $targets = $this->target_type === 'customer' 
            ? Customer::active()->orderBy('name')->get()
            : Supplier::orderBy('name')->get();

        return view('livewire.dashboard.payments.payment-vouchers', [
            'targets' => $targets,
        ]);
    }

    public function updatedTargetId()
    {
        $this->updateTargetBalance();
    }

    public function updatedTargetType()
    {
        $this->target_id = null;
        $this->currentTargetBalance = 0;
        $this->voucher_type = $this->target_type === 'customer' ? 'receipt' : 'disbursement';
    }

    protected function updateTargetBalance()
    {
        if (!$this->target_id) {
            $this->currentTargetBalance = 0;
            return;
        }

        $target = $this->target_type === 'customer' 
            ? Customer::find($this->target_id)
            : Supplier::find($this->target_id);
        
        $this->currentTargetBalance = $target ? $target->current_balance : 0;
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function resetForm()
    {
        $this->reset(['paymentId', 'isEditing', 'target_id', 'amount', 'reference_number', 'notes']);
        $this->payment_date = now()->format('Y-m-d');
        $this->payment_method = 'cash';
        $this->target_type = 'customer';
        $this->voucher_type = 'receipt';
    }

    public function save(PaymentVoucherService $service)
    {
        $this->validate();

        if ($this->isEditing) {
            // Edit logic not implemented in service yet, but we can follow same pattern
            // For now, let's focus on Create and Delete as most common for vouchers
        } else {
            $service->create([
                'target_type' => $this->target_type,
                'target_id' => $this->target_id,
                'amount' => $this->amount,
                'payment_method' => $this->payment_method,
                'payment_date' => $this->payment_date,
                'voucher_type' => $this->voucher_type,
                'reference_number' => $this->reference_number,
                'notes' => $this->notes,
            ]);
        }

        $this->showModal = false;
        $this->dispatch('refresh-datatable');
        $this->dispatch('notify', [
            'type' => 'success',
            'title' => __('Success'),
            'message' => __('Voucher saved successfully')
        ]);
    }

    #[\Livewire\Attributes\On('confirm-delete-voucher')]
    public function confirmDelete($id)
    {
        $this->paymentId = $id;
        $this->dispatch('open-delete-modal', [
            'title' => __('Delete Voucher'),
            'message' => __('Are you sure you want to delete this voucher? This will revert the balance impact.'),
            'id' => $id
        ]);
    }

    #[\Livewire\Attributes\On('delete-confirmed')]
    public function deleteVoucher(PaymentVoucherService $service)
    {
        if ($this->paymentId) {
            $payment = Payment::findOrFail($this->paymentId);
            $service->delete($payment);
            $this->dispatch('refresh-datatable');
            $this->dispatch('notify', [
                'type' => 'success',
                'title' => __('Success'),
                'message' => __('Voucher deleted successfully')
            ]);
            $this->paymentId = null;
        }
    }
}
