<?php

namespace App\Http\Controllers\Purchases;

use Illuminate\Http\Request;
use Illuminate\Support\Number;
use App\Events\PurchaseCreated;
use App\Traits\NextPreviousTrait;
use App\Models\Purchases\Purchases;
use App\Services\SelectDataService;
use App\Http\Controllers\Controller;
use App\Services\CustomFieldService;
use App\Services\PurchaseKPIService;
use Illuminate\Support\Facades\Auth;
use App\Models\Products\StockLocation;
use App\Services\PurchaseOrderService;
use App\Models\Purchases\PurchaseInvoice;
use App\Models\Purchases\PurchaseReceipt;
use App\Models\Companies\CompaniesContacts;
use App\Services\PurchaseCalculatorService;
use App\Models\Companies\CompaniesAddresses;
use App\Models\Purchases\PurchasesQuotation;
use App\Models\Products\StockLocationProducts;
use App\Models\Purchases\PurchaseReceiptLines;
use App\Http\Requests\Purchases\StorePurchaseRequest;
use App\Http\Requests\Purchases\UpdatePurchaseRequest;
use App\Http\Requests\Purchases\UpdatePurchaseInvoiceRequest;
use App\Http\Requests\Purchases\UpdatePurchaseReceiptRequest;
use App\Http\Requests\Purchases\UpdatePurchaseQuotationRequest;

class PurchasesController extends Controller
{
    use NextPreviousTrait;

    protected $SelectDataService;
    protected $purchaseKPIService;
    protected $customFieldService;
    protected $purchaseOrderService;

    /**
     * Constructor to initialize services.
     *
     * @param SelectDataService $SelectDataService
     * @param PurchaseKPIService $purchaseKPIService
     * @param CustomFieldService $customFieldService
     * @param PurchaseOrderService $purchaseOrderService
     */
    public function __construct(
            SelectDataService $SelectDataService, 
            PurchaseKPIService $purchaseKPIService,
            CustomFieldService $customFieldService,
            PurchaseOrderService $purchaseOrderService,
        ){
        $this->SelectDataService = $SelectDataService;
        $this->purchaseKPIService = $purchaseKPIService;
        $this->customFieldService = $customFieldService;
        $this->purchaseOrderService = $purchaseOrderService;
    }
    
    /**
     * Display the purchase request view.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function request()
    {   
        return view('purchases/purchases-request');
    }

    /**
     * Display the purchase quotation view.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function quotation()
    {    
        $data['purchasesQuotationDataRate'] = $this->purchaseKPIService->getPurchaseQuotationDataRate();
                                                            
        return view('purchases/purchases-quotation')->with('data',$data);
    }

    /**
     * Display the purchase view with various data.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function purchase()
    {   
        $factory = app('Factory');
        $data['purchasesDataRate'] = $this->purchaseKPIService->getPurchasesDataRate();
        $data['purchaseMonthlyRecap'] = $this->purchaseKPIService->getPurchaseMonthlyRecap();

        $topRatedSuppliers = $this->purchaseKPIService->getTopRatedSuppliers();
        $sortedByAvgReceptionDelay = $this->purchaseKPIService->getAverageReceptionDelayBySupplier();
        $top5FastestSuppliers = $sortedByAvgReceptionDelay->take(5);
        $top5SlowestSuppliers = $sortedByAvgReceptionDelay->reverse()->take(5);

        $topProducts = $this->purchaseKPIService->getTopProducts();
        $averageAmount = Number::currency($this->purchaseKPIService->getAverageAmount(),$factory->curency, config('app.locale'));
        $totalPurchaseLineCount = $this->purchaseKPIService->getTotalPurchaseCount();
        $totalPurchasesAmount = Number::currency($this->purchaseKPIService->getTotalPurchaseAmount(),$factory->curency, config('app.locale'));

        $userSelect = $this->SelectDataService->getUsers();
        $CompanieSelect = $this->SelectDataService->getSupplier();

        $LastPurchase =   $this->purchaseOrderService->generatePurchaseCode();

        return view('purchases/purchases-index', [
                                                    'topRatedSuppliers' => $topRatedSuppliers,
                                                    'top5FastestSuppliers' => $top5FastestSuppliers,
                                                    'top5SlowestSuppliers' => $top5SlowestSuppliers,
                                                    'topProducts' => $topProducts,
                                                    'averageAmount' => $averageAmount,
                                                    'totalPurchaseLineCount' => $totalPurchaseLineCount,
                                                    'totalPurchasesAmount' => $totalPurchasesAmount,
                                                    'userSelect' => $userSelect,
                                                    'CompanieSelect' => $CompanieSelect,
                                                    'code' => $LastPurchase,
                                                    'label' => $LastPurchase,
                                                ])->with('data',$data);
    }

    /**
     * Display a specific purchase quotation.
     *
     * @param PurchasesQuotation $id
     * @return \Illuminate\Contracts\View\View
     */
    public function showQuotation(PurchasesQuotation $id)
    {   
        $CompanieSelect = $this->SelectDataService->getSupplier();
        $AddressSelect = $this->SelectDataService->getAddress($id->companies_id);
        $ContactSelect = $this->SelectDataService->getContact($id->companies_id);
        list($previousUrl, $nextUrl) = $this->getNextPrevious(new PurchasesQuotation(), $id->id);
                                    
        return view('purchases/purchases-quotation-show', [
            'PurchaseQuotation' => $id,
            'CompanieSelect' => $CompanieSelect,
            'AddressSelect' => $AddressSelect,
            'ContactSelect' => $ContactSelect,
            'previousUrl' =>  $previousUrl,
            'nextUrl' =>  $nextUrl,
        ]);
    }

    /**
     * Display a specific purchase.
     *
     * @param Purchases $id
     * @return \Illuminate\Contracts\View\View
     */
    public function showPurchase(Purchases $id)
    {   
        $CompanieSelect = $this->SelectDataService->getSupplier();
        $AddressSelect = $this->SelectDataService->getAddress($id->companies_id);
        $ContactSelect = $this->SelectDataService->getContact($id->companies_id);
        $PurchaseCalculatorService = new PurchaseCalculatorService($id);
        $totalPrice = $PurchaseCalculatorService->getTotalPrice();
        $subPrice = $PurchaseCalculatorService->getSubTotal();
        $vatPrice = $PurchaseCalculatorService->getVatTotal();
        list($previousUrl, $nextUrl) = $this->getNextPrevious(new Purchases(), $id->id);
        $CustomFields = $this->customFieldService->getCustomFieldsWithValues('purchase', $id->id);

        return view('purchases/purchases-show', [
            'Purchase' => $id,
            'CompanieSelect' => $CompanieSelect,
            'AddressSelect' => $AddressSelect,
            'ContactSelect' => $ContactSelect,
            'totalPrices' => $totalPrice,
            'subPrice' => $subPrice,
            'vatPrice' => $vatPrice,
            'previousUrl' =>  $previousUrl,
            'nextUrl' =>  $nextUrl,
            'CustomFields' => $CustomFields,
        ]);
    }

    /**
     * Prepare purchase data for storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|bool The prepared purchase data or false if defaults are missing.
     */
    protected function preparePurchaseData($request)
    {
        $defaultAddress = CompaniesAddresses::getDefault(['companies_id' => $request->companies_id]);
        $defaultContact = CompaniesContacts::getDefault(['companies_id' => $request->companies_id]);

        if (!$defaultAddress || !$defaultContact) {
            return false;
        }

        $purchaseData = $request->only('code', 'label', 'companies_id', 'user_id');
        $purchaseData['companies_addresses_id'] = $defaultAddress->id;
        $purchaseData['companies_contacts_id'] = $defaultContact->id;
        $purchaseData['user_id'] = Auth::id();

        return $purchaseData;
    }

    /**
     * Store a new bank purchase.
     *
     * @param \App\Http\Requests\Purchases\StorePurchaseRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeBankPurchase(StorePurchaseRequest  $request)
    {
        $purchaseData = $this->preparePurchaseData($request);

        if ($purchaseData === false) {
            return redirect()->back()->with('error', 'No default settings fount for address, contact or accounting vat');
        }
    
        $purchaseOrderCreated = Purchases::create($purchaseData);
    
        return redirect()->route('purchases.show', ['id' => $purchaseOrderCreated->id])
                            ->with('success', 'Successfully created new purchase order');
    }

    /**
     * Store a new purchase order from a request for quotation (RFQ).
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id The ID of the purchase quotation.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storePurchaseOrderFromRFQ(Request $request, $id)
    { 
        if (!$request->PurchaseQuotationLine) {
            return redirect()->back()->withErrors(['msg' => 'no lines selected']);
        }
    
        $PurchasesQuotationData = PurchasesQuotation::findOrFail($id);
    
        $purchaseOrder = $this->purchaseOrderService->createPurchaseOrderFromQuotation($PurchasesQuotationData);
    
        if (!$purchaseOrder) {
            return redirect()->back()->withErrors(['msg' => 'Something went wrong (like no default settings for address, contact or accounting vat)']);
        }
    
        $statusUpdate = $this->purchaseOrderService->getStatusUpdate();
    
        if (!$statusUpdate) {
            return redirect()->back()->with('error', 'No status "Supplied" or "In progress" in kanban for defining progress');
        }
    
        $this->purchaseOrderService->processPurchaseQuotationLines(
            $request->PurchaseQuotationLine,
            $request->PurchaseQuotationLineTaskid,
            $purchaseOrder,
            $statusUpdate->id,
            $request->PurchaseQuotationLinePrice,
        );

        // Déclencher l'événement PurchaseCreated
        event(new PurchaseCreated($PurchasesQuotationData));
    
        return redirect()->route('purchases.show', ['id' => $purchaseOrder->id])
                            ->with('success', 'Successfully created new purchase order');
    }

    /**
     * Display a specific purchase receipt.
     *
     * @param PurchaseReceipt $id
     * @return \Illuminate\Contracts\View\View
     */
    public function showReceipt(PurchaseReceipt $id)
    {   
        
        $StockLocationList = StockLocation::all();
        $StockLocationProductList = StockLocationProducts::all();
        list($previousUrl, $nextUrl) = $this->getNextPrevious(new PurchaseReceipt(), $id->id);

        $averageReceptionDelay = PurchaseReceiptLines::join('purchase_lines', 'purchase_receipt_lines.purchase_line_id', '=', 'purchase_lines.id')
                                                    ->where('purchase_receipt_lines.purchase_receipt_id', $id->id) // Filtrer par bon de réception spécifique
                                                    ->selectRaw('AVG(DATEDIFF(purchase_receipt_lines.created_at, purchase_lines.created_at)) AS avg_reception_delay')
                                                    ->first();

        return view('purchases/purchases-receipt-show', [
            'PurchaseReceipt' => $id,
            'previousUrl' =>  $previousUrl,
            'nextUrl' =>  $nextUrl,
            'StockLocationList' =>  $StockLocationList,
            'StockLocationProductList' =>  $StockLocationProductList,
            'averageReceptionDelay' => $averageReceptionDelay->avg_reception_delay
        ]);
    }

    /**
     * Display a specific purchase invoice.
     *
     * @param PurchaseInvoice $id
     * @return \Illuminate\Contracts\View\View
     */
    public function showInvoice(PurchaseInvoice $id)
    {   
        list($previousUrl, $nextUrl) = $this->getNextPrevious(new PurchaseInvoice(), $id->id);

        return view('purchases/purchases-invoice-show', [
            'PurchaseInvoice' => $id,
            'previousUrl' =>  $previousUrl,
            'nextUrl' =>  $nextUrl,
        ]);
    }
    

    /**
     * Update a purchase order.
     *
     * @param \App\Http\Requests\Purchases\UpdatePurchaseRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePurchase(UpdatePurchaseRequest $request)
    {
        $Purchases = Purchases::find($request->id);
        $Purchases->label=$request->label;
        $Purchases->companies_id=$request->companies_id;
        $Purchases->companies_contacts_id=$request->companies_contacts_id;
        $Purchases->companies_addresses_id=$request->companies_addresses_id;
        $Purchases->comment=$request->comment;
        $Purchases->save();
        
        return redirect()->route('purchases.show', ['id' =>  $Purchases->id])->with('success', 'Successfully updated purchase order');
    }

    /**
     * Update a purchase quotation.
     *
     * @param \App\Http\Requests\Purchases\UpdatePurchaseQuotationRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePurchaseQuotation(UpdatePurchaseQuotationRequest $request)
    {
        $PurchasesQuotation = PurchasesQuotation::find($request->id);
        $PurchasesQuotation->label=$request->label;
        $PurchasesQuotation->statu=$request->statu;
        $PurchasesQuotation->companies_id=$request->companies_id;
        $PurchasesQuotation->companies_contacts_id=$request->companies_contacts_id;
        $PurchasesQuotation->companies_addresses_id=$request->companies_addresses_id;
        $PurchasesQuotation->delay=$request->delay;
        $PurchasesQuotation->comment=$request->comment;
        $PurchasesQuotation->save();
        
        return redirect()->route('purchases.quotations.show', ['id' =>  $PurchasesQuotation->id])->with('success', 'Successfully updated purchase quotation');
    }

    /**
     * Update a purchase receipt.
     *
     * @param \App\Http\Requests\Purchases\UpdatePurchaseReceiptRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePurchaseReceipt(UpdatePurchaseReceiptRequest $request)
    {
        $PurchaseReceipt = PurchaseReceipt::find($request->id);
        $PurchaseReceipt->label=$request->label;
        $PurchaseReceipt->statu=$request->statu;
        $PurchaseReceipt->delivery_note_number=$request->delivery_note_number;
        $PurchaseReceipt->comment=$request->comment;
        $PurchaseReceipt->save();
        
        return redirect()->route('purchase.receipts.show', ['id' =>  $PurchaseReceipt->id])->with('success', 'Successfully updated reciept');
    }

    /**
     * Update the reception control of a purchase receipt.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id The ID of the purchase receipt.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateReceptionControl(Request $request, $id)
    {
        $purchaseReceipt = PurchaseReceipt::findOrFail($id);

        $purchaseReceipt->reception_controlled = 1;
        $purchaseReceipt->reception_control_date = now(); 
        $purchaseReceipt->reception_control_user_id = Auth::id(); 
        $purchaseReceipt->save();

        return redirect()->back()->with('success', 'Contrôle de réception mis à jour avec succès.');
    }
    
    /**
     * Update a purchase invoice.
     *
     * @param \App\Http\Requests\Purchases\UpdatePurchaseInvoiceRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePurchaseInvoice(UpdatePurchaseInvoiceRequest $request)
    {
        $PurchaseInvoice = PurchaseInvoice::find($request->id);
        $PurchaseInvoice->label=$request->label;
        $PurchaseInvoice->statu=$request->statu;
        $PurchaseInvoice->comment=$request->comment;
        $PurchaseInvoice->save();
        
        return redirect()->route('purchase.invoices.show', ['id' =>  $PurchaseInvoice->id])->with('success', 'Successfully updated reciept');
    }

    /**
     * Display the waiting receipt view.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function waintingReceipt()
    {    
        return view('purchases/purchases-wainting-receipt');
    }

    /**
     * Display the receipt view with data.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function receipt()
    {    
        $data['PurchaseReciepCountDataRate'] = $this->purchaseKPIService->getPurchaseReciepCountDataRate();
        return view('purchases/purchases-receipt')->with('data',$data);
    }

    /**
     * Display the waiting invoice view.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function waintingInvoice()
    {    
        return view('purchases/purchases-wainting-invoice');
    }

    /**
     * Display the invoice view with data.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function invoice()
    {   
        $data['purchasesDataRate'] = $this->purchaseKPIService->getPurchaseInvoiceDataRate();
        $data['purchaseInvoiceMonthlyRecap'] = $this->purchaseKPIService->getPurchaseInvoiceMonthlyRecap();
                                                            
        return view('purchases/purchases-invoice')->with('data',$data);
    }
}
