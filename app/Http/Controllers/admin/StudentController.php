<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\groups;
use App\Models\Students;
use App\Models\User;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    private static $user;
    public function __construct()
    {
        self::$user = auth()->guard('admin')->user();
    }
    
    function store(Request $request){
        $formInputs = $request->validate([
            'name'=>'required|min:3',
            'phone_number1'=>'required|min:10',
            'phone_number2'=>'nullable|min:10',
            'location'=>'required',
            'medical'=>'nullable',
            'points'=>'nullable|numeric',            
        ]);
       
        $user = Students::create($formInputs);
        // $token = $user->createToken('MyAppToken')->plainTextToken;

        return response()->json([
            'message' => 'Welcome ',
            'result' => "done",
        ]);
        
    }
   
   
    function index(){
        return response()->json([
            'returnCode'=>200,
            'message' => "",
            'result' => self::$user->level=="admin"? Students::with("groups")->get(): Students::with("groups")->where("level",self::$user->level)->get(),
        ]);
    }
    public function changeUserGroup(Request $request,Students $student){
        $formInputs = $request->validate([
            'newGroupId'=>'nullable',
            
        ]);
        $student->groups_id = $formInputs["newGroupId"];
        $student->save();
        return response()->json([
            'returnCode'=>200,
            'message' => "done",
            'result' => "",
        ]);
    }   

    public function addRange(Request $request){

    }

}
