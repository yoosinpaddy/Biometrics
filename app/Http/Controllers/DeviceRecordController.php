<?php

namespace App\Http\Controllers;

use App\Models\DeviceRecord;
use Illuminate\Http\Request;

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
        // dd();
        $person1=[
            'eno'=>'1221',//work number
            'idcard'=>'id1',//ID number
            'cardid'=>'card 1',//card number
            'uuid'=>'xx1',//uuid
            'name'=>'fname sname',//names
            'type'=>1,//Type 0 Delete 1 Add Update Note: Deleting a person will delete them along with their access rights configuration.
        ];
        $person2=[
            'eno'=>'2112',//work number
            'idcard'=>'id2',//ID number
            'cardid'=>'card 2',//card number
            'uuid'=>'xx2',//uuid
            'name'=>'fname2 sname2',//names
            'type'=>1,//Type 0 Delete 1 Add Update Note: Deleting a person will delete them along with their access rights configuration.
        ];
        $data=[
            'employeeList'=> [$person1,$person2],
            'count'=>2,//People List page size (get the people list by page, this is the pageSize per page)
            'sum'=>2,//Total number of records in the population list
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
}
