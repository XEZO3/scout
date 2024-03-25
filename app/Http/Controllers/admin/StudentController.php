<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\groups;
use App\Models\Students;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            "level"=>auth()->guard('admin')->user()->level == "admin"?"required":"nullable",            
        ]);
       $formInputs['level'] = (auth()->guard('admin')->user()->level != "admin")?auth()->guard('admin')->user()->level:$formInputs['level']; 
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
    function edit($id){
        return response()->json([
            'message' => '',
            'result' => Students::with("groups")->where("id",$id)->first(),
        ]);
    }
    function update(Request $req,$id){
        $student = Students::find($id);
        $data = $req->validate([
            'name'=>'required|min:3',
            'phone_number1'=>'required|min:10',
            'phone_number2'=>'nullable|min:10',
            'location'=>'required',
            'medical'=>'nullable',
            'points'=>'nullable|numeric',
        ]);
        DB::beginTransaction();
        try {
        $student->update($data);
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
    public function destroy($id){
        Students::destroy($id);
        return response()->json([
            'returnCode'=>200,
            'message' => "done",
            'result' => "",
        ],200);
    }   

}
