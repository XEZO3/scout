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
            $response ['students'] = Students::where("level",$level)->get();
              return $this->response(true,"",$response);    
        }else{
            $response ['students'] = Students::orderBy('created_at')->get();
             return  $this->response(true,"", $response);    
        }
    }
    public function create($data){
        $user = Students::create($data);
        return $this->response(true,);
    }
}