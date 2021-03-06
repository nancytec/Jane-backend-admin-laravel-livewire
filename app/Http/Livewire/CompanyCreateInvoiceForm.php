<?php

namespace App\Http\Livewire;

use App\Models\Contact;
use App\Models\Invoice;
use App\Models\InvoicePaymentMethod;
use App\Models\InvoiceProduct;
use App\Models\InvoiceService;
use App\Models\Product;
use App\Models\Service;
use App\Models\Worker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class CompanyCreateInvoiceForm extends Component
{
    public $to;
    public $worker;

    public $invoice_number;
    public $date_issued;
    public $due_date;
    public $account_number;
    public $account_name;
    public $payment_methods = [];
    public $note;
    public $status;


    // Product Item selection params
    public $items;
    public $product_item;
    public $product_unit_price;
    public $product_quantity = 1;
    public $product_item_note;
    public $product_total_price = 0;
    // Selected Item
    public $product_selected_item;
    public $product_selected_items = [];


    // Service Item selection params
    public $service_items;
    public $service_item;
    public $service_unit_price;
    public $service_unit;
    public $service_volume = 1;
    public $service_item_note;
    public $service_total_price = 0;
    // Selected Item
    public $service_selected_item;
    public $service_selected_items = [];



    // contacts and workers
    public $contacts;
    public $workers;
    public $products;
    public $services;

    public function updated($field){
       $this->computeProductData();
       $this->computeServiceData();


        $this->validateOnly($field, [
            'to'                => 'required|numeric',
            'worker'            => 'required|numeric',
            'date_issued'       => 'required|string|max:255',
            'due_date'          => 'required|string|max:255',
            'payment_methods'   => 'required|array'
        ]);
    }

    public function mount(){
        $this->fetchUsersData();
    }

    public function addProductItem(){
        // Validate inputs
        $checkInput = $this->validateProductItemInputs();
        if ($checkInput['errorCode'] != 'SUCCESS'){
            return $this->emit('alert', ['type' => 'error', 'message' => $checkInput['errorCode']]);
        }

        $item = [
          'id'                => $this->product_selected_item->id,
          'name'              => $this->product_selected_item->name,
          'quantity'          => $this->product_quantity,
          'unit_price'        => $this->product_unit_price,
          'total_price'       => $this->product_total_price,
          'note'              => $this->product_item_note
        ];

        $productIsPresent = false;
        if (count($this->product_selected_items) > 0){
            foreach ($this->product_selected_items as $p_item){
                if ($this->product_selected_item->name == $p_item['name']){
                    $productIsPresent = true;
                }
            }
        }
        if ($productIsPresent){
            // Add to the existing product
            return $this->emit('alert', ['type' => 'error', 'message' => 'You have already selected this product']);
        }
        array_push($this->product_selected_items, $item);

        // Clear the input
        $this->product_item        = '';
        $this->product_item_note   = '';
        $this->product_quantity    = 1;
        $this->product_unit_price  = '';
        $this->product_total_price = 0;
    }

    public function validateProductItemInputs(){
        // Check if an item is selected
        if (!$this->product_item){
            return [
                'errorCode' => 'Please select an item'
            ];
        }

        // Check f quantity is less that one
        if (!$this->product_quantity || $this->product_quantity < 1){
            return [
                'errorCode'   => 'Please select a valid qantity'
            ];
        }

        if (!$this->product_item_note){
            return [
                'errorCode'   => 'Please add a description to the item'
            ];
        }

        return [
            'errorCode' => 'SUCCESS'
        ];
    }

    public function computeProductData(){
        // Compute item unit price
        if ($this->product_item && $this->product_quantity > 0){
            $this->product_selected_item = Product::find($this->product_item);
            $this->product_total_price = $this->product_selected_item->price * $this->product_quantity;

            $this->product_unit_price = $this->product_selected_item->price;
        }
    }



    public function addServiceItem(){
        // Validate inputs
        $checkInput = $this->validateServiceItemInputs();
        if ($checkInput['errorCode'] != 'SUCCESS'){
            return $this->emit('alert', ['type' => 'error', 'message' => $checkInput['errorCode']]);
        }

        $item = [
            'id'                => $this->service_selected_item->id,
            'name'              => $this->service_selected_item->name,
            'usage'             => $this->service_selected_item->usage_unit,
            'volume'            => $this->service_volume,
            'unit_price'        => $this->service_unit_price,
            'total_price'       => $this->service_total_price,
            'note'              => $this->service_item_note
        ];

        $serviceIsPresent = false;
        if (count($this->service_selected_items) > 0){
            foreach ($this->service_selected_items as $s_item){
                if ($this->service_selected_item->name == $s_item['name']){
                    $serviceIsPresent = true;
                }
            }
        }
        if ($serviceIsPresent){
            // Add to the existing product
            return $this->emit('alert', ['type' => 'error', 'message' => 'You have already selected this service']);
        }
        array_push($this->service_selected_items, $item);

        // Clear the input
        $this->service_item        = '';
        $this->service_item_note   = '';
        $this->service_volume      = 1;
        $this->service_unit        = '';
        $this->service_unit_price  = '';
        $this->service_total_price = 0;
    }

    public function validateServiceItemInputs(){
        // Check if an item is selected
        if (!$this->service_item){
            return [
                'errorCode' => 'Please select a service'
            ];
        }

        // Check f quantity is less that one
        if (!$this->service_volume || $this->service_volume < 1){
            return [
                'errorCode'   => 'Please select a valid service volume'
            ];
        }

        if (!$this->service_item_note){
            return [
                'errorCode'   => 'Please add a description to the service'
            ];
        }

        return [
            'errorCode' => 'SUCCESS'
        ];
    }

    public function computeServiceData(){
        // Compute item unit price

        if ($this->service_item && $this->service_volume){
            $this->service_selected_item = Service::find($this->service_item);
            $this->service_total_price = $this->service_selected_item->price * $this->service_volume;
            $this->service_unit = $this->service_selected_item->usage_unit;

            $this->service_unit_price = $this->service_selected_item->price;
        }
    }


    public function fetchUsersData(){
        $this->contacts  = Contact::where('company_id', Auth::user()->company_id)->get();
        $this->workers   = Worker::where('company_id', Auth::user()->company_id)->get();
        $this->products  = Product::where('company_id', Auth::user()->company_id)->get();
        $this->services  = Service::where('company_id', Auth::user()->company_id)->get();

        $this->fetchFormData();
    }

    public function fetchFormData(){
        $this->invoice_number = Str::random(4).''.sprintf("%06d", mt_rand(1, 999999999));
    }

    public function generateInvoice(){
        // Either of the product or service must be selected
        if (count($this->product_selected_items) < 1 && count($this->service_selected_items) < 1){
            return $this->emit('alert', ['type' => 'error', 'message' => 'You have to select at least one product or service']);
        }

        $this->validate([
            'to'                => 'required|numeric',
            'worker'            => 'required|numeric',
            'date_issued'       => 'required|max:255',
            'due_date'          => 'required|max:255',
            'payment_methods'   => 'required|array'
        ]);

        // Calculate total price for products and services
        $productTotalPrice = 0;
        if (count($this->product_selected_items) > 0){
            foreach ($this->product_selected_items as $p_item){
                $productTotalPrice = $productTotalPrice + $p_item['total_price'];
            }
        }
        $serviceTotalPrice = 0;
        if (count($this->service_selected_items) > 0){
            foreach ($this->service_selected_items as $s_item){
                $serviceTotalPrice = $serviceTotalPrice + $s_item['total_price'];
            }
        }


        // Create the invoice
        $invoice = Invoice::create([
            'invoice_code'      => $this->invoice_number,
            'company_id'        => Auth::user()->company_id,
            'contact_id'        => $this->to,
            'worker_id'         => $this->worker,
            'creator_id'        => Auth::user()->id, // User_id not worker id

            'date_issued'       => $this->date_issued,
            'due_date'          => $this->due_date,

            'products_total_price'      => $productTotalPrice,
            'services_total_price'      => $serviceTotalPrice,

            'status'                    => $this->status,
            'signature_code'            => Str::random(40)
        ]);

        // create payment methods table
        if (count($this->payment_methods) > 0){
            foreach ($this->payment_methods as $method){
                InvoicePaymentMethod::create([
                    'invoice_id'    => $invoice->id,
                    'method'        => $method
                ]);
            }
        }

        // Create invoice products
        if (count($this->product_selected_items) > 0){
            foreach ($this->product_selected_items as $p_item) {
                InvoiceProduct::create([
                    'invoice_id'        => $invoice->id,
                    'product_id'        => $p_item['id'],
                    'quantity'          => $p_item['quantity'],
                    'unit_price'        => $p_item['unit_price'],
                    'total_price'       => $p_item['total_price'],
                    'description'       => $p_item['note']
                ]);
            }
        }

        // Create invoice services
        if (count($this->service_selected_items) > 0){
            foreach ($this->service_selected_items as $s_item) {
                InvoiceService::create([
                    'invoice_id'        => $invoice->id,
                    'service_id'        => $s_item['id'],
                    'usage'             => $s_item['usage'],
                    'volume'            => $s_item['volume'],
                    'unit_price'        => $s_item['unit_price'],
                    'total_price'       => $s_item['total_price'],
                    'description'       => $s_item['note']
                ]);
            }
        }

        $this->resetExcept([
            'workers',
            'products',
            'services',
            'contacts'
        ]);
        return $this->emit('alert', ['type' => 'success', 'message' => 'Invoice generated']);
    }

    public function render()
    {
        return view('livewire.company.components.company-create-invoice-form');
    }
}
