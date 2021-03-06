<?php

namespace App\Http\Controllers\View\Company;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Http\Request;

class CompanyServiceViewController extends Controller
{
    public function services (){
        $data = [
            'title' => 'Company services',
            'keywords' => 'Company services',
            'description' => 'Company services',
        ];
        return view('livewire.Company.pages.Company-services-page', ['data' => $data]);
    }

    public function serviceDetails ($id){
        $service = Service::find($id);
        $data = [
            'title' => 'Company services',
            'keywords' => 'Company services',
            'description' => 'Company services',
        ];
        return view('livewire.company.pages.company-service-details-page', ['data' => $data, 'service' => $service]);
    }
}
