<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use App\Services\TaskService;
use App\Models\Companies\Companies;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use App\Models\Purchases\PurchaseLines;
use App\Services\DocumentCodeGenerator;
use App\Services\PurchaseReceiptService;
use App\Models\Purchases\PurchaseReceipt;

class PurchasesWaintingReceipt extends Component
{
    public $companies_id = '';
    public $sortField = 'id'; // default sorting field
    public $sortAsc = true; // default sort direction
    
    public $LastReceipt= null;
    public $document_type = 'RC';
    public $deliveryNoteNumber;

    public $PurchasesWaintingReceiptLineslist;
    public $code, $label, $user_id; 
    public $updateLines = false;
    public $CompanieSelect = [];
    public $data = [];
    public $qty = [];
    protected $taskService;
    protected $purchaseReceiptService;
    protected $documentCodeGenerator;

    public function __construct()
    {
        // Resolve the service via the Laravel container
        $this->taskService = App::make(TaskService::class);
        $this->purchaseReceiptService = App::make(PurchaseReceiptService::class);
        $this->documentCodeGenerator = App::make(DocumentCodeGenerator::class);
    }

    // Validation Rules
    protected function rules()
    { 
        return [
            'code' =>'required|unique:purchase_receipts',
            'label' =>'required',
            'companies_id'=>'required',
            'user_id'=>'required',
        ];
    }
    
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortAsc = !$this->sortAsc; 
        } else {
            $this->sortAsc = true; 
        }
        $this->sortField = $field;
    }

    public function mount() 
    {
        $this->user_id = Auth::id();
        // get last id
        $this->LastReceipt =  PurchaseReceipt::latest()->first();
        $purchaseReceiptId = $this->LastReceipt ? $this->LastReceipt->id : 0;
        $this->code = $this->documentCodeGenerator->generateDocumentCode('purchase-receipt', $purchaseReceiptId);
        $this->label = $this->code;

        $this->CompanieSelect = Companies::select('id', 'label', 'code')->where('statu_supplier', '=', 2)->orderBy('code')->get();
    }

    public function render()
    {
        $userSelect = User::select('id', 'name')->get();
        //Select task where statu is open and only purchase type
        $PurchasesWaintingReceiptLineslist = $this->PurchasesWaintingReceiptLineslist = PurchaseLines::orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
                                                                                        ->where('receipt_qty','<=', 'qty')
                                                                                        ->whereHas('purchase', function($q){
                                                                                            $q->where('companies_id','like', '%'.$this->companies_id.'%');
                                                                                        })
                                                                                        ->get();

        return view('livewire.purchases-wainting-receipt', [
            'PurchasesWaintingReceiptLineslist' => $PurchasesWaintingReceiptLineslist,
            'userSelect' => $userSelect,
        ]);
    }

    public function storeReciep()
    {
        $this->validate();

        try {
            // Données du reçu d'achat
            $receiptData = [
                'code' => $this->code,
                'label' => $this->label,
                'companies_id' => $this->companies_id,
                'delivery_note_number' => $this->deliveryNoteNumber,
                'user_id' => $this->user_id,
            ];

            // Appel au service pour la création du reçu
            $ReceiptCreated = $this->purchaseReceiptService->createPurchaseReceipt($this->data, $receiptData);

            return redirect()->route('purchase.receipts.show', ['id' => $ReceiptCreated->id])
                ->with('success', 'Successfully created new receipt');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}