<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\activities;
use App\Models\Students;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\LeadersService\ActivityService;
class ActivitiesController extends Controller
{
    private $activityService;
    public function __construct(ActivityService $activityService) {
        $this->activityService = $activityService;
    }
    public function index(){
       
       $response = $this->activityService->getAll();
        return response()->json($response);
    }
    public function store(Request $req){
        $data = $req->validate([
            'title'=>"required",
            "level"=>auth()->guard('admin')->user()->level == "admin"?"required":"nullable",
            'place'=>"required",
        ]);
        $response = $this->activityService->create($data);
        return response()->json($response);
    }
    public function takeAbsent(Request $req, $activity){
        $response = $this->activityService->takeAbsent($activity);
        return response()->json($response);
    }
    public function setAbsent(Request $req, activities $activity){
        $validation = $req->validate([
            "absent_user"=>"nullable"
       ]);
       $absent_id = $validation["absent_user"] ??null;
       $response = $this->activityService->takeAbsent($activity,$absent_id);
       return response()->json($response);
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
