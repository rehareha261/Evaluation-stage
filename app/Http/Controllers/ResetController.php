<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\Database\ResetDatabase;
use Illuminate\Http\JsonResponse;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;

    class ResetController extends Controller{
        private $resetDatabase;

        public function __construct(ResetDatabase $reset){
            $this->resetDatabase = $reset;
        }

        public function resetDatabase():JsonResponse{
            $currentUser = Auth::user();
            if($currentUser->hasRole(Role::OWNER_ROLE)){
                $this->resetDatabase->resetDatabase();
                return response()->json(["Message" => "La base de donnee a ete reinitialise"]);
            }
            return response()->json(['Message' => "Seuls les administrateurs peuvent reinitialiser la base de donnee"]);

        }

    }


?>