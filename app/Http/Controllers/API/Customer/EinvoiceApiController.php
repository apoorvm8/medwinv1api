<?php

namespace App\Http\Controllers\API\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Customer\Einvoice\DeleteEinvoiceRequest;
use App\Http\Resources\API\Customer\EinvoicesResource;
use App\Services\EinvoiceService;
use Illuminate\Http\Request;

class EinvoiceApiController extends Controller
{
    private $eInvoiceService;

    public function __construct(EinvoiceService $eInvoiceService){
        $this->eInvoiceService = $eInvoiceService;
    } 

    /**
     * @desc Function to get the customers
     */
    public function get(Request $request) {
        $data = $this->eInvoiceService->getCustomerEinvoices($request->query());
        return response(['success' => true, 'msg' => 'Customers E-Invoices Retrieved', 'data' => [
            'einvoices' => new EinvoicesResource($data),
        ]]);
    }

    /**
     * @desc Funcion to delete invoice of customer
     */
    public function delete(DeleteEinvoiceRequest $request, $id) {
        $this->eInvoiceService->deleteEinvoice($id);
        return response(['success' => true, 'msg' => 'Customer E-Invoice deleted', 'data' => []]);
    }
}
