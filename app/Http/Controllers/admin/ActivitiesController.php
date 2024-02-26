<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\activities;
use App\Models\Students;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActivitiesController extends Controller
{
    private static $user;
    public function __construct()
    {
        self::$user = auth()->guard('admin')->user();
    }

    public function index(){
       
        
        if(self::$user->level ==null ||empty(self::$user->level)){
            $activities['activity'] = activities::with("students")->orderBy('created_at')->get();
        }else{
            $activities['activity'] = activities::with("students")->where("level",self::$user->level)->orderBy('created_at')->get();
        }
        $activities['total_user'] = Students::count();
        return response()->json([
            'message' => "",
            'result' => $activities,
        ],200);
    }
    public function store(Request $req){
        DB::beginTransaction();
        try {
        $data = $req->validate([
            'title'=>"required",
            "absent_user"=>"nullable"
        ]);
        $activities = activities::create([
            "title"=>$data['title']
        ]);
        $activities->Students()->attach($data['absent_user']);
        
        foreach ($data["absent_user"] as $record) {
            $user = Students::find($record);
            $user->absens +=1;
            $user->save(); 
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
            'result' => activities::with("students")->find($id),
        ]);
    }
    public function update($id,Request $req){
        
        
        $data = $req->validate([
            'title'=>"required",
            "absent_user"=>"nullable",
            "prev_user"=>"nullable",
            
        ]);
        DB::beginTransaction();
        try {
        $activities = activities::find($id);
        $activities->update([
            "title"=>$data['title']
        ]);
        
       $nAbsent =  $activities->Students()->sync($data['absent_user'])['attached'];
        
        foreach ($nAbsent as $record) {
            $user = Students::find($record);
            $user->absens +=1;
            $user->save(); 
        }
        foreach ($data["prev_user"] as $record) {
            $user = Students::find($record);
            $user->absens -=1;
            $user->save(); 
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
    public function destroy($id){
        activities::destroy($id);
        return response()->json([
            'returnCode'=>200,
            'message' => "done",
            'result' => "",
        ],200);
    }

}
