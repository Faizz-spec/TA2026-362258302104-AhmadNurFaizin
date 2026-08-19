<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class TemplateController extends Controller
{
    public function index()
    {
        return Inertia::render('Templete/Index');
    }

    public function button()
    {
        return Inertia::render('Templete/Button');
    }

    public function form()
    {
        return Inertia::render('Templete/Form');
    }

    public function table()
    {
        return Inertia::render('Templete/Table');
    }

    public function card()
    {
        return Inertia::render('Templete/Card');
    }

    public function pagination()
    {
        return Inertia::render('Templete/Pagination');
    }
}
