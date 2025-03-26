<?php

namespace App\DTOs;

use App\Models\Client;
use App\Models\Contact;
use App\Models\Industry;
use App\Models\Project;
use App\Models\User;
use App\Services\Exception\ImportException;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class ClientProjectDTO{

    public $project_title;

    public $client_name;

    public $client;

    public $project;

    public function setProject_title($value){
        if(is_null($value) || $value === ''){
            throw new Exception("Le projet n'a pas de titre");
        }
        $this->project_title = $value;
    }
    public function setClient_name($value){
        if(is_null($value) || $value === ''){
            throw new Exception("Le projet n'a pas de client");
        }
        $this->client_name = $value;
    }

    public function __construct() {}


    public function generateClient($adminId){
        $client = new Client(
                [
                    'external_id' => Uuid::uuid4()->toString(),
                    'company_name' => $this->client_name,
                    'user_id' => $adminId,
                    'industry_id' => Industry::inRandomOrder()->first()->id
                ]
        );
        return $client;
    }

    public function generateContact($clientId){
        $contact = new Contact(
                [
                    'external_id' => Uuid::uuid4()->toString(),
                    'name' => Str::random(10),
                    'email' => Str::random(10)."@gmail.com",
                    'client_id' => $clientId,
                    'is_primary' => 1
                ]
        );
        return $contact;
    }


    public function generateProject($adminId, $statusOpenId){
        $startDate = time();
        $endDate = strtotime('2030-12-01');
        $randomTimestamp = rand($startDate, $endDate);
        $deadline = date('Y-m-d', $randomTimestamp);
        $project = new Project(
            [
                'external_id' => Uuid::uuid4()->toString(),
                'title' => $this->project_title,
                'description' => Str::random(30),
                'status_id' => $statusOpenId,
                'user_assigned_id' => $adminId,
                'user_created_id' => $adminId,
                'client_id' => $this->client->id,
                'deadline' => $deadline
            ]
        );
        return $project;
    }

    public function traiter($adminId, $statusOpenId, $ligne){
        $this->client = Client::where('company_name', '=', $this->client_name)->first(); 
        if(is_null($this->client)){
            $this->client = $this->generateClient($adminId);
            $this->client->save();
            $contact = $this->generateContact($this->client->id);
            $contact->save();
        }
        $this->project = Project::where('title', '=', $this->project_title)->first();
        if(is_null($this->project)){
            $this->project = $this->generateProject($adminId, $statusOpenId);
            $this->project->save();
        }else{
            throw new ImportException("", ["Ligne ".$ligne." : le projet ".$this->project_title." existe deja"]);
        }
    }

}


?>