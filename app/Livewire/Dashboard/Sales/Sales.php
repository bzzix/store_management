<?php

namespace App\Livewire\Dashboard\Sales;

use Livewire\Component;
use App\Models\SaleInvoice;
use App\Services\SalesInvoiceService;

class Sales extends Component
{
    public $confirmingCancellation = false;
    public $confirmingDeletion = false;
    public $cancellingInvoiceId;
    public $deletingInvoiceId;

    protected $listeners = [
        'refresh-sales' => '$refresh',
        'cancel-invoice' => 'confirmCancellation',
        'delete-invoice' => 'confirmDelete',
    ];

    public function openCreateModal()
    {
        $this->dispatch('create-invoice');
    }

    public function confirmDelete($id)
    {
        $this->deletingInvoiceId = $id;
        $this->confirmingDeletion = true;
    }

    public function deleteInvoice()
    {
        abort_if(!auth()->user()->can('sale_invoices_delete'), 403);
        
        try {
            $invoice = SaleInvoice::findOrFail($this->deletingInvoiceId);
            $invoice->delete();
            
            $this->dispatch('notify', [
                'type' => 'success',
                'title' => __('Success'),
                'msg' => __('Invoice deleted successfully')
            ]);
            
            $this->confirmingDeletion = false;
            $this->dispatch('refresh-sales');
            $this->dispatch('refresh-datatable');
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'title' => __('Error'),
                'msg' => $e->getMessage()
            ]);
        }
    }

    public function confirmCancellation($id)
    {
        $this->cancellingInvoiceId = $id;
        $this->confirmingCancellation = true;
    }

    public function cancelInvoice(SalesInvoiceService $service)
    {
        abort_if(!auth()->user()->can('sales_cancel'), 403);
        
        try {
            $invoice = SaleInvoice::findOrFail($this->cancellingInvoiceId);
            $service->cancel($invoice);
            
            $this->dispatch('notify', [
                'type' => 'success',
                'title' => __('Success'),
                'msg' => __('Invoice cancelled successfully')
            ]);
            
            $this->confirmingCancellation = false;
            $this->dispatch('refresh-sales');
            $this->dispatch('refresh-datatable');
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'title' => __('Error'),
                'msg' => $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        return view('livewire.dashboard.sales.sales')
            ->layout('dashboard.layouts.master');
    }
}
