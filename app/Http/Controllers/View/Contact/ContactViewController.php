<?php

namespace App\Http\Controllers\View\Contact;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactViewController extends Controller
{

    public function dashboard (){
        $data = [
            'title' => 'Contact dashboard',
            'keywords' => 'Contact dashboard',
            'description' => 'Contact dashboard'
        ];
        return view('livewire.contact.pages.contact-dashboard-page', ['data' => $data]);
    }

    public function profile (){
        $data = [
            'title' => 'Contact profile',
            'keywords' => 'Contact profile',
            'description' => 'Contact profile'
        ];
        return view('livewire.contact.pages.contact-my-user-info-page', ['data' => $data, 'user' => Auth::user()]);
    }

}
