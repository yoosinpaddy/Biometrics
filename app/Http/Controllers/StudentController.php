<?php

namespace App\Http\Controllers;

use App\Models\Stream;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use PhpParser\Node\Expr\Cast\Array_;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }
    public function home(Request $request)
    {
        $user=Auth::user();
        $new=0;
        $inProgress=0;
        $submited=0;
        $complete=0;

        $formated_classes=Array();
        $streams=Stream::all();
        $classes=Student::select('class')->where('class','!=','9')->groupBy('class')->get();
        foreach ($classes as $class) {
            $myclass=[];
            $studentCount=0;
            $studentCount=sizeof(Student::where('class', '=', $class->class)
            ->get());
            $myclass['class']=$class->class;
            $myclass['stream']='(All)';
            $myclass['stream_id']=0;
            $myclass['_count']=$studentCount;
            array_push($formated_classes,$myclass);
            $studentCount=0;
            foreach ($streams as $stream) {
                $studentCount=sizeof(Student::where('class', '=', $class->class)
                ->where('stream', '=', $stream->id)
                ->get());
                $myclass['class']=$class->class;
                $myclass['stream']=$stream->name;
                $myclass['stream_id']=$stream->id;
                $myclass['_count']=$studentCount;
                array_push($formated_classes,$myclass);
            }
        }
        // dd($formated_classes);
        // $notifications=Notification::where('receiver_id','=',$user->id)->get();
        // $new=sizeof(Order::where(function($query) {
        //     return $query->where('status', '=', 'new')
        //         ->orWhere('status', '=', 'paid');
        // })->where('customer_id', '=', $user->id)->get());
        // $newOrders=Order::where('status', '=', 'new')->where('customer_id','=',$user->id)->get();
        // dd($newOrders);
        // $new=sizeof($newOrders);
        // $inProgress=sizeof(Order::where('customer_id', '=', $user->id)->where('status', '=', 'assigned')->where('status', '=', 'revision')->get());
        // $submited=sizeof(Order::where('customer_id', '=', $user->id)->where('status', '=', 'submitted')->get());
        // $complete=sizeof(Order::where('customer_id', '=', $user->id)->where('status', '=', 'completed')->get());
        return view('school.home',['formated_classes'=>$formated_classes]);
    }

    public function login(Request $request)
    {
        if ($request->method() == 'GET') {
            return view('school.login', []);
        }
        $validate = $request->validate([
            'email' => ['required', 'max:255'],
            'password' => ['required', 'max:255'],
        ]);

        if (sizeof(User::where('email', '=', $request->email)->get()) > 0) {
            $user = User::where('email', '=', $request->email)->first();
            if($user->password==null){

                $user->password = Hash::make($request->password);
                $user->update();
            }else{

            }
        }
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $request->session()->regenerate();

            return redirect(route('school.home'));
        }

        return back()->withErrors([
            'email' => 'Check the details and try again',
        ]);
    }

    public function register(Request $request)
    {

        if ($request->method() == 'GET') {
            return view('school.register', []);
        }
        $validate = $request->validate([
            'name' => ['required', 'max:255'],
            'phone' => ['required', 'max:255'],
            'email' => ['required', 'max:255'],
            'password' => ['required', 'max:255'],
            'rpassword' => ['required', 'max:255'],
            'terms' => ['required', 'max:255'],
        ]);
        if ($request->password!==$request->rpassword) {
            $errors=array();
                $errors=['Passwords do not match'];
            return redirect()->back()->withErrors($errors);
        }
        $user = new User();
        $exists = false;
        if (sizeof(User::where('email', '=', $request->email)->get()) > 0) {
            $user = User::where('email', '=', $request->email)->first();
            if($user->password==null){

            }else{
            $errors=array();
                $errors=['Email already exists!'];
            return redirect()->back()->withErrors($errors);

            }
        }
        // dd($user);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->password = Hash::make($request->password);
        if($user->save()){

            if (Auth::attempt(['email' => $user->email, 'password' => $request->password])) {
                $request->session()->regenerate();

                return redirect(route('school.home'));
            }

            return back()->withErrors([
                'email' => 'Reg. successful try logging in',
            ]);
            return view('school.login', []);

        }
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function show(Student $student)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function edit(Student $student)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Student $student)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function destroy(Student $student)
    {
        //
    }
}
