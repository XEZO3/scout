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
            $activities['activity'] = activities::where("level", $level)
            ->with(['students:id'])
            ->orderBy('created_at')
            ->get();        
        }else{
            $activities['activity'] = activities::with("students:id")->orderBy('created_at')->get();
        }
            $activities['total_user'] = Students::where("level",$level)->count();

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
        // $this->setAbsent($activities,$data['absent_user']);

        DB::commit();
        return $this->response(true,"",array());
        }catch (\Exception $e) {
            DB::rollBack();
            return $this->response(false,"Error creating activity and associating users",array());
        }       
    }
    public function setAbsent($activities,$ids){
        $activities->Students()->attach($ids);   
        foreach ($ids as $record) {
            $user = Students::find($record);
            $user->absens +=1;
            $user->save(); 
        }
    }
}