<?php

namespace App\Services\LeadersService;
use App\Models\activities;
use App\Models\Students;
use App\Services\BaseService;

class StudentService extends BaseService
{
    public function getAll(){
        $level = auth()->guard('admin')->user()->level;
        if($level!="admin"){
            $students = Students::where("level",$level)->get();
              return $this->response(true,"",$students);    
        }else{
            $students = Students::orderBy('created_at')->get();
             return  $this->response(true,"", $students);    
        }
    }
    public function create($data){
        $user = Students::create($data);
        return $this->response(true,);
    }
}