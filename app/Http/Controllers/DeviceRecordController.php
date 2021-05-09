<?php

namespace App\Http\Controllers;

use App\Models\DeviceRecord;
use App\Models\FaceRecord;
use App\Models\Guardian;
use App\Models\Smstemplete;
use App\Models\Staff;
use App\Models\StaffFaceRecord;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DeviceRecordController extends Controller
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
        $record = new DeviceRecord();
        $record->data = 'store|'.implode("|",$request->all());
        $record->save();
        return json_encode([
            'code'=>200,
            'success'=>true,
            'messsage'=>'successful',
            'data'=>(time()*1000)
        ]);
    }
    public function recordUpload(Request $request)
    {
        $data = json_decode($request->data, TRUE)[0];
        // dd($data['eno']);
        $upi_no=$data['eno'];
        $time_taken=$data['scandatetime'];
        $device_serial=$data['macno'];
        $temperature=$data['temperature'];
        $event=$data['operatorno'];//operatorno Punch Type :
                                    // face_0: successful face recognition
                                    // face_2: stranger brushes face
                                    // card_0: swipe card successfully
                                    // card_2: Invalid card
                                    // idcard_0: Witness matching successful
                                    // idcard_2: witness match failure
                                    // faceAndcard_0: successful card+face dual authentication
                                    // faceAndcard_2: card+face dual authentication failure
                                    // open_0: button to open the door.
                                    // qrcode_0: QR Code Success
                                    // qrcode_2: unregistered QR code (no counterpart for QR code)
                                    // password_0: password to open the door
                                    // -2020-02-25: Adding a record of failed witness comparison
                                    // --2020-09-14 (version 2.2.2): add open door password

        if($event=='face_0'||$event=='card_0'||$event=='faceAndcard_0'){
            $student=Student::where('upi_no','=',$upi_no)->get()->first();
            // dd($student->id);
            if($student!=null){
                $faceRecord=new FaceRecord();
                $faceRecord->upi_no=$upi_no;
                $faceRecord->time_taken=$time_taken;
                $faceRecord->device_serial=$device_serial;
                $faceRecord->event=$event;
                $faceRecord->temperature=$temperature;


                $guardian=Guardian::where('student_id','=',$student->id)->where('should_notify','=','true')->first();
                if($guardian!=null){
                    $faceR=FaceRecord::where('upi_no','=',$upi_no)
                    ->whereDate('created_at', Carbon::today())
                    ->orderby('id','DESC')
                    ->first();
                    // dd($faceR);
                    if($faceR!=null){
                        //we have a record
                        //check if a record is already present within the past 30 minutes
                        $input=$faceR->time_taken;
                        $input2=$time_taken;
                        $input = floor($input /1000 / 60);
                        $input2 = floor($input2 /1000 / 60);
                        if($input2-$input<10){

                            // dd('<10');
                            //recent record taken
                            //Ignore
                            // $faceRecord->save();
                            // $this->sendSms($guardian,$faceRecord,$time_taken,'second');
                        }else{
                            //check if its the second record

                            if (sizeof(FaceRecord::where('upi_no', '=', $upi_no)
                            ->whereDate('created_at', Carbon::today())
                            ->get()) ==1) {
                                // dd('second');
                                $faceRecord->status='exit';
                                $faceRecord->save();
                                $this->sendSms($guardian,$faceRecord,$time_taken,'second');
                            }else{
                                // dd(sizeof(FaceRecord::where('upi_no', '=', $upi_no)
                                // ->whereDate('created_at', Carbon::today())
                                // ->get()));
                                $faceRecord->save();
                            }
                        }


                    }else{
                        //no record
                        // dd('first');
                        $faceRecord->status='enter';
                        $faceRecord->save();
                        $this->sendSms($guardian,$faceRecord,$time_taken,'first');
                    }

                    // return back()->with('success', 'Sms sent successfully');
                }
            }else{
                $staff=Staff::where('staff_id','=',$upi_no)->get()->first();
                if ($staff!=null) {
                    $faceRecord=new StaffFaceRecord();
                $faceRecord->reg_no=$upi_no;
                $faceRecord->time_taken=$time_taken;
                $faceRecord->device_serial=$device_serial;
                $faceRecord->staff_type=$staff->type;
                $faceRecord->event=$event;
                $faceRecord->temperature=$temperature;



                    $faceR=StaffFaceRecord::where('reg_no','=',$upi_no)
                    ->whereDate('created_at', Carbon::today())
                    ->orderby('id','DESC')
                    ->first();
                    // dd($faceR);
                    if($faceR!=null){
                        //we have a record
                        //check if a record is already present within the past 30 minutes
                        $input=$faceR->time_taken;
                        $input2=$time_taken;
                        $input = floor($input /1000 / 60);
                        $input2 = floor($input2 /1000 / 60);
                        if($input2-$input<10){

                            // dd('<10');
                            //recent record taken
                            //Ignore
                            // $faceRecord->save();
                            // $this->sendSms($guardian,$faceRecord,$time_taken,'second');
                        }else{
                            //check if its the second record

                            if (sizeof(StaffFaceRecord::where('reg_no', '=', $upi_no)
                            ->whereDate('created_at', Carbon::today())
                            ->get()) ==1) {
                                // dd('second');
                                $faceRecord->status='exit';
                                $faceRecord->save();
                            }else{
                                // dd(sizeof(FaceRecord::where('upi_no', '=', $upi_no)
                                // ->whereDate('created_at', Carbon::today())
                                // ->get()));
                                $faceRecord->save();
                            }
                        }


                    }else{
                        //no record
                        // dd('first');
                        $faceRecord->status='enter';
                        $faceRecord->save();
                    }
                }
            }
        }
        $record = new DeviceRecord();
        $record->data = 'recordUpload|'.implode("|",$request->all());
        $record->save();
        $myData=[
            'openDoor'=>1,//Whether open relay, 0: no, 1: open
            'tipSpeech'=>"Thanks for verifying",//Display and voice over content It can be used \n to indicate a line swap, such as: "Zhang San Hello this consumption of $20 \n balance of $800."
            'state'=>2,//0: Display text and broadcast voice at the same time
                        //1: Only text is displayed, no voice is broadcasted.
                        //2: Do not display text, only voice
            'openDoor'=>1,//Whether
        ];
        return json_encode([
            'code'=>200,
            'success'=>true,
            'messsage'=>'successful',
            'data'=>$myData
        ]);
    }
    public function dataPull(Request $request)
    {
        $record = new DeviceRecord();
        $record->data = 'dataPull |'.implode("|",$request->all());
        $record->save();
        $pageNumber=null;
        if($request->has('pageNumber')){
            $pageNumber=$request->pageNumber;
        }
        $students_count=Student::withTrashed()->where('class','!=','9')->get();
        $students=Student::withTrashed()->where('class','!=','9')->paginate(100, ['*'], 'page', $pageNumber);

        $formated_students=[];
        foreach ($students as $student) {
            array_push($formated_students, (object)[
                'eno'=>$student->upi_no,//work number
                'idcard'=>$student->getStream->name,//ID number-use as stream
                'cardid'=>$student->class,//card number-use as class
                'uuid'=>$student->id,//uuid
                'name'=>$student->first_name.' '.$student->surname,//names
                'type'=>$student->deleted_at==NULL?1:0,//Type 0 Delete 1 Add Update Note: Deleting a person will delete them along with their access rights configuration.
            ]);
        }
        $data=[
            'employeeList'=> $formated_students,
            'count'=>100,//People List page size (get the people list by page, this is the pageSize per page)
            'sum'=>sizeof($students_count),//Total number of records in the population list
        ];
        $myResponse=json_encode([
            'code'=>200,
            'success'=>true,
            'messsage'=>'successful',
            'data'=>$data
        ]);
        return response($myResponse)
        ->header('Content-Type', 'application/json');
    }

    public function dataPullBack(Request $request)
    {
        $record = new DeviceRecord();
        $record->data = 'dataPullBack|'.implode("|",$request->all());
        $record->save();
        return json_encode([
            'code'=>200,
            'success'=>true,
            'messsage'=>'successful',
            'data'=>(time()*1000)
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DeviceRecord  $deviceRecord
     * @return \Illuminate\Http\Response
     */
    public function show(DeviceRecord $deviceRecord)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DeviceRecord  $deviceRecord
     * @return \Illuminate\Http\Response
     */
    public function edit(DeviceRecord $deviceRecord)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DeviceRecord  $deviceRecord
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DeviceRecord $deviceRecord)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DeviceRecord  $deviceRecord
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeviceRecord $deviceRecord)
    {
        //
    }

    public function sendSms($guardian,$face_record,$time,$sms_time){

        $date = date("h:i a",($time/1000));
        $new_time = date("h:i a", strtotime('+3 hours', strtotime($date)));
        $temp=round($face_record->temperature,1);
        if($sms_time=='first'){
            $templete1=Smstemplete::where('id','=',1)->get()->pluck('content');

            $message1="Dear $guardian->fname, your child ".$face_record->student->first_name." ".$face_record->student->surname."  UPI:".$face_record->student->upi_no." has arrived at school at $new_time with a temperature of $temp ° ". $templete1[0];
            // dd($templete);
        }else{
            $templete1=Smstemplete::where('id','=',2)->get()->pluck('content');
            $message1="Dear $guardian->fname, your child ".$face_record->student->first_name." ".$face_record->student->surname." UPI:".$face_record->student->upi_no." has left school for home at $new_time with a temperature of $temp ° ". $templete1[0];
        }
// dd($new_time);
        $response=Http::asForm()->post('https://quicksms.advantasms.com/api/services/sendsms',[
            'apikey'=>$_ENV['SMS_API_KEY'],
            'partnerID'=>$_ENV['SMS_PATNER_ID'],
            'shortcode'=>$_ENV['SMS_SHORT_CODE'],
            'message'=>$message1,
            'mobile'=>$guardian->phone,
        ]);
        if($response->successful()){
            // dd($response->json()['responses'][0]['response-description']);
            // return back()->with('success', $response->json()['responses'][0]['response-description']);
        }

        // Determine if the status code is >= 400...
        if($response->failed()){

            // return back()->withErrors([
            //     'message' => 'Something went wrong, could not send sms',
            // ]);
        }

        // Determine if the response has a 400 level status code...
        if($response->clientError()){

            // return back()->withErrors([
            //     'message' => 'Something went wrong, could not send sms',
            // ]);
        }

        // Determine if the response has a 500 level status code...
        if($response->serverError()){

            // return back()->withErrors([
            //     'message' => 'Something went wrong, could not send sms',
            // ]);
        }

    }
}
