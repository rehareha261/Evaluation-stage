<?php

namespace App\Services\Database;

use Illuminate\Support\Facades\DB;

    class ResetDatabase{
        public function resetDatabase(){
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            $tablesExclues = ["migrations", "roles", "industries", "departments", "settings", "permissions", "business_hours", "permission_role", "statuses"];

            $tables = DB::select("SHOW TABLES;");

            $databaseName = env('DB_DATABASE');

            $key = "Tables_in_{$databaseName}";

            foreach ($tables as $table) {
                $tableName = $table->$key;
                if ($tableName==="users") {
                    DB::table($tableName)->where('id','!=',1)->delete();
                }
                else if($tableName==="department_user" || $tableName==="role_user"){
                    DB::table($tableName)->where('user_id','!=',1)->delete();
                }
                else if (!in_array($tableName,$tablesExclues)) {
                    DB::table($tableName)->truncate();
                }
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            
        }
    }


?>