<?php

namespace App\Http\Controllers\View\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CompanyViewController extends Controller
{
    public function dashboard (){
        $data = [
            'title' => 'Company dashboard',
            'keywords' => 'Admin dashboard',
            'description' => 'Admin dashboard'
        ];
        return view('livewire.company.pages.company-dashboard-page', ['data' => $data]);
    }

    public function usersList (){
        $data = [
            'title' => 'Users',
            'keywords' => 'Users',
            'description' => 'Users'
        ];
        return view('livewire.company.pages.companies-users-list-page', ['data' => $data]);
    }

}
