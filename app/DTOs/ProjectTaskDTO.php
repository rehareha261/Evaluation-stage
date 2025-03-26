<?php

namespace App\DTOs;

use App\Models\Client;
use App\Models\Industry;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Services\Exception\ImportException;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class ProjectTaskDTO{

    public $project_title;

    public $task_title;

    public $task;

    public $project;

    public function setProject_title($value){
        if(is_null($value) || $value === ''){
            throw new Exception("Le projet n'a pas de titre");
        }
        $this->project_title = $value;
    }
    public function setTask_title($value){
        if(is_null($value) || $value === ''){
            throw new Exception("Le task n'a pas de titre");
        }
        $this->task_title = $value;
    }

    public function __construct() {}


    public function generateTask($adminId, $statusOpenId){
        $startDate = time();
        $endDate = strtotime('2030-12-01');
        $randomTimestamp = rand($startDate, $endDate);
        $deadline = date('Y-m-d', $randomTimestamp);
        $task = new Task(
            [
                'external_id' => Uuid::uuid4()->toString(),
                'title' => $this->task_title,
                'description' => Str::random(30),
                'status_id' => $statusOpenId,
                'user_assigned_id' => $adminId,
                'user_created_id' => $adminId,
                'client_id' => $this->project->client()->first()->id,
                'project_id' => $this->project->id,
                'deadline' => $deadline
            ]
        );
        return $task;
    }

    public function traiter($adminId, $statusOpenId, $ligne){
        $this->project = Project::where('title', '=', $this->project_title)->first();
        if(is_null($this->project)){
            throw new ImportException("", ["Ligne ".$ligne." : le projet ".$this->project_title." n'existe pas"]);
        }else{
            $this->task = $this->generateTask($adminId, $statusOpenId);
            $this->task->save();
        }
    }

}


?>