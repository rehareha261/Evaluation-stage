<?php
namespace App\Http\Controllers;

use App\DTOs\UserDto;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Csv\ImportDataService;
use App\Services\Csv\ImportService;
use App\Services\Exception\ImportException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid;

class ImportCsvController extends Controller
{
    protected $csvService;

    public function __construct(ImportDataService $csvService)
    {
        $this->csvService = $csvService;
    }

    public function index()
    {
        return view('csv.import');
    }

    public function importData(Request $request)
    {
        $request->validate([
            'csv_project' => 'required|file|mimes:csv,txt',
            'csv_task' => 'required|file|mimes:csv,txt',
            'csv_invoice' => 'required|file|mimes:csv,txt'
        ]);

        $csv_project = $request->file('csv_project');
        $csv_task = $request->file('csv_task');
        $csv_invoice = $request->file('csv_invoice');
        $countProject = 0;
        $countTask = 0;
        $countInvoice = 0;
        $error = [];
        $message = [];
        DB::beginTransaction();
        try{
            try{
                $countProject = $this->csvService->importClientProject($csv_project);
                $message[] = $countProject." projects imported successfully";
            }catch(ImportException $e){
                $error = array_merge($error, $e->getDetails());
            }
            try{
                $countTask = $this->csvService->importProjectTask($csv_task);
                $message[] = $countTask." tasks imported successfully";
            }catch(ImportException $e){
                $error = array_merge($error, $e->getDetails());
            }
            try{
                $countInvoice = $this->csvService->importInvoiceData($csv_invoice);
                $message[] = $countInvoice." invoices imported successfully";
            }catch(ImportException $e){
                $error = array_merge($error, $e->getDetails());            
            }
            if(count($error) > 0){
                throw new ImportException("", $error);
            }
            DB::commit();
            return back()->with('success', implode("\n", $message));
        }catch (ImportException $e) {
            DB::rollBack();
            $errorMessages = implode("\n", $e->getDetails());
            return back()->with('error', $errorMessages);
        }
        
        
    }
}
?>