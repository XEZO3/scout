<?php

namespace App\Services\LeadersService;
use App\Models\activities;
use App\Models\Students;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;

class ActivityService extends BaseService
{
    public function getAll(){
        $level = auth()->guard('admin')->user()->level;
        if($level!="admin"){
            $activities = Activity::where("level", $level)
            ->with(['students' => function($query) {
                $query->select('students_id', 'activities_id');
            }])
            ->orderBy('created_at')
            ->get();        }else{
            $activities['activity'] = activities::with("students")->orderBy('created_at')->get();
        }
            $activities['total_user'] = Students::where("level",$level)->count();
            $activities['activity']['absent_user'] =activities::with("students")->where("level",$level)->orderBy('created_at')->count();

        return $this->response(true,"",$activities);
    }
    public function create($data){
        DB::beginTransaction();
        $level = auth()->guard('admin')->user()->level;
        try {
        $activities = activities::create([
            "title"=>$data['title'],
            'level'=>$level!="admin"?$level:$data['level'],
        ]);
        $activities->Students()->attach($data['absent_user']);
        
        foreach ($data["absent_user"] as $record) {
            $user = Students::find($record);
            $user->absens +=1;
            $user->save(); 
        }

        DB::commit();
        return $this->response(true,"",array());
        }catch (\Exception $e) {
            DB::rollBack();
            return $this->response(false,"Error creating activity and associating users",array());
        }       
    }
}