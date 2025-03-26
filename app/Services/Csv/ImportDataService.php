<?php

namespace App\Services\Csv;

use App\DTOs\ClientProjectDTO;
use App\DTOs\InvoiceDTO;
use App\DTOs\ProjectTaskDTO;
use App\Models\Status;
use App\Models\User;
use App\Services\Exception\ImportException;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use ReflectionClass;
use ReflectionProperty;

class ImportDataService
{

    protected $csvService;

    public function __construct(ImportService $csvService)
    {
        $this->csvService = $csvService;
    }
    public function importClientProject($file){
        $fileName = 'import_client_project' . time() . '.csv';
        $file->move(storage_path('app/imports'), $fileName);
        $fullPath = storage_path('app/imports/' . $fileName);
        $error = [];
        $clientProjectDtos = [];
        try {
            try{
                $clientProjectDtos = $this->csvService->importCsv($fullPath, ClientProjectDTO::class, $fileName);
            }catch(ImportException $ee){
                $error = array_merge($error, $ee->getDetails());
            }
            $admin = User::first();
            $adminId = $admin->id;
            $openStatus = 11;

            $count = 2;
            foreach ($clientProjectDtos as $clientProject) {
                try{
                    $clientProject->traiter($adminId, $openStatus, $count);
                }catch(ImportException $ee){
                    $error = array_merge($error, $ee->getDetails());
                }
                $count++;
            }
            
            if(count($error) > 0){
                throw new ImportException("", $error);
            }
            return count($clientProjectDtos);
        } catch (ImportException $e) {
            throw $e;
        }
    }
    public function importProjectTask($file){
        $fileName = 'import_project_task' . time() . '.csv';
        $file->move(storage_path('app/imports'), $fileName);
        $fullPath = storage_path('app/imports/' . $fileName);
        $error = [];
        $projectTasks = [];
        try {
            try{
                $projectTasks = $this->csvService->importCsv($fullPath, ProjectTaskDTO::class, $fileName);
            }catch(ImportException $ee){
                $error = array_merge($error, $ee->getDetails());
            }
            $admin = User::first();
            $adminId = $admin->id;
            $openStatus = 1;

            $count = 2;
            foreach ($projectTasks as $projectTask) {
                try{
                    $projectTask->traiter($adminId, $openStatus, $count);
                }catch(ImportException $ee){
                    $error = array_merge($error, $ee->getDetails());
                }
                $count++;
            }
            
            if(count($error) > 0){
                throw new ImportException("", $error);
            }
            return count($projectTasks);
        } catch (ImportException $e) {
            throw $e;
        }
    }    

    public function importInvoiceData($file){
        $fileName = 'import_invoice_data' . time() . '.csv';
        $file->move(storage_path('app/imports'), $fileName);
        $fullPath = storage_path('app/imports/' . $fileName);
        $error = [];
        $invoices = [];
        try {
            try{
                $invoices = $this->csvService->importCsv($fullPath, InvoiceDTO::class, $fileName);
            }catch(ImportException $ee){
                $error = array_merge($error, $ee->getDetails());
            }
            $admin = User::first();
            $adminId = $admin->id;
            $openStatus = 7;


            $count = 2;
            foreach ($invoices as $invoice) {
                try{
                    $invoice->traiter($adminId, $openStatus, $count);
                    $count++;
                }catch(ImportException $ee){
                    $error = array_merge($error, $ee->getDetails());
                }
                $count++;
            }
            
            if(count($error) > 0){
                throw new ImportException("", $error);
            }

            return count($invoices);
        } catch (ImportException $e) {
            throw $e;
        }
    }    
}
?>