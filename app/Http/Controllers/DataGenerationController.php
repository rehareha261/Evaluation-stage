<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\Appointment;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Offer;
use App\Models\Payment;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class DataGenerationController extends Controller
{
    public function generateData()
    {
        factory(Client::class, 20)->create();
        factory(Lead::class, 5)->create();

        factory(Task::class, 10)->create();
        factory(Appointment::class, 9)->create();
        factory(Project::class, 7)->create();
        factory(Absence::class, 5)->create();

        factory(Invoice::class, 3)->create();
        factory(Payment::class, 2)->create();

        return response()->json(['message' => 'Données générées avec succès']);
    }
}