<?php

namespace App\Http\Controllers;

use App\Support\PreventiveStatus;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ExamController extends Controller
{
    public function __invoke(Request $request): Response
    {
        return Inertia::render('Exams/Index', [
            'preventive' => PreventiveStatus::for($request->user()),
        ]);
    }
}
