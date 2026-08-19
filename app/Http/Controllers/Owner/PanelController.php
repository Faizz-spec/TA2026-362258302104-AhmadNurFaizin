<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class PanelController extends Controller
{
    public function index()
    {
        // resources/js/Pages/Owner/Owner.vue
        return Inertia::render('Owner/Owner');
    }
}
