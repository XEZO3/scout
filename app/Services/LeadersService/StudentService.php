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
              return $this->response(true,"",Students::where('level',$level)->get());    
        }else{
             return  $this->response(true,"",Students::orderBy('created_at')->get());    
        }
    }
    public function create($data){
        $user = Students::create($data);
        return $this->response(true,);
    }
}