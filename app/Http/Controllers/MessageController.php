<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Auth;
use App\Message;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    function users(Request $request)
    {
        if($request->ajax())
        {
            $data = User::where('id','!=',Auth::user()->id)->get();
            echo json_encode($data);
        }
    }
    
    function messages(Request $request,$id)
    {
        $userId = Auth::user()->id; 
        if($request->ajax())
        {
            $rec  = Message::where('receiverId',$userId)
                    ->where('senderId',$id);
                    
            $data = Message::where('receiverId',$id)
                    ->where('senderId',$userId)
                    ->union($rec)
                    ->orderBy('id')
                    ->get();
            echo json_encode($data);
        }
    }
    
    function createData(Request $request)
    {
        if($request->ajax())
        {
            $data = array(
                'senderId'      =>  $request->senderId,
                'receiverId'    =>  $request->receiverId,
                'messageText'   =>  $request->message,
                'created_at'    =>  now(),
                'updated_at'    =>  now(),
            );
            Message::insert($data);
            echo $request->message;
        }
    }
}