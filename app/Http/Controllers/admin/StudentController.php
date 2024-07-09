<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\groups;
use App\Models\Students;
use App\Models\User;
use App\Services\LeadersService\StudentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    private static $user;
    private $studentService;
    public function __construct(StudentService $studentService)
    {
        $this->studentService = $studentService;
        self::$user = auth()->guard('admin')->user();
    }
    
    function store(Request $request){
        $level = auth()->guard('admin')->user()->level;
        $formInputs = $request->validate([
            'name'=>'required|min:3',
            'phone_number1'=>'required|min:10',
            'phone_number2'=>'nullable|min:10',
            'location'=>'required',
            'medical'=>'nullable',
            'points'=>'nullable|numeric',
            "level"=> $level == "admin"?"required":"nullable",            
        ]);
       $formInputs['level'] = ( $level != "admin")? $level:$formInputs['level']; 
        $user = Students::create($formInputs);
        return response()->json([
            'message' => 'Welcome ',
            'result' => "done",
        ]);
        
    }
   
   
    function index(){
        $response = $this->studentService->getAll();
        return response()->json($response);
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
