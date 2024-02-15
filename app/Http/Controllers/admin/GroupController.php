<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\groups;
use App\Models\Students;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GroupController extends Controller
{
    public function index(){
        $groups = groups::with("students")->orderBy('created_at')->get();
        return response()->json([
            'message' => "",
            'result' => $groups,
        ],200);  
    }
    public function store(Request $req){
       
            DB::beginTransaction();
            try {
            $data = $req->validate([
                'name'=>"required",
                "students"=>"nullable"
            ]);
            $group=groups::create([
                "name"=>$data['name']
            ]);
            
            foreach ($data["students"] as $record) {
                $student = Students::find($record);
                $student->groups_id =$group->id;
                $student->save(); 
            }
    
            DB::commit();
            return response()->json([
                'message' => "done",
                'result' => "",
            ],200);
        
            }catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['message' => 'Error creating activity and associating users: ' . $e->getMessage()], 500);
            }       
        
    }
    public function edit($id){
        return response()->json([
            'returnCode'=>200,
            'message' => "",
            'result' => groups::with("students")->find($id),
        ]);
    }
    public function update($id,Request $req){
        
        $group = groups::find($id);
        $data = $req->validate([
            'name'=>"required",
        ]);
        DB::beginTransaction();
        try {
        $group->update(["name"=>$data['name']]);

        DB::commit();
        return response()->json([
            'message' => "done",
            'result' => "",
        ],200);
    
        }catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error creating activity and associating users: ' . $e->getMessage()], 500);
        }       
    }
    public function destroy($id){
        groups::destroy($id);
        return response()->json([
            'returnCode'=>200,
            'message' => "done",
            'result' => "",
        ],200);
    }
}
