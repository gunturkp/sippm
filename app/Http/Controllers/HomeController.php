<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\MasterInventory;
use App\Mail;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // return view('home');
        if (\Auth::user())
        {
            if (\Auth::user()->role == 1)
            {
                return redirect()->guest('/admin');
            }else if (\Auth::user()->role == 2)
            {
                return redirect()->guest('/kasubag');
            }else if (\Auth::user()->role == 3)
            {
                return redirect()->guest('/teknisi');
            }else if (\Auth::user()->role == 0)
            {
                return redirect()->guest('/user');
            }
        }else
        {
            return redirect()->guest('/');
        }
    }

    public function GetInventoryRuangan(Request $request){
        $inventory = MasterInventory::where('master_inventory.id_ruangan','=',$request->input('id_ruangan'))
            ->join('master_ruangan','master_ruangan.id_ruangan','=','master_inventory.id_ruangan')
            ->get();
        
        return response()->json($inventory);
    }

    public function GetDataMail(Request $request){
        Mail::where(function($q) use ($request){
            $q->where('user_id_sender',\Auth::user()->id)
                ->where('user_id_receiver',$request->user_id)
                ->where('is_read',0);
            })
        ->orWhere(function($q) use ($request){
            $q->where('user_id_sender',$request->user_id)
                ->where('user_id_receiver',\Auth::user()->id)
                ->where('is_read',0);
            })
            ->update(['is_read' => 1]);

        $datamail = Mail::where(function($q) use ($request){
                $q->where('user_id_sender',\Auth::user()->id)
                    ->where('user_id_receiver',$request->user_id);
                })
            ->orWhere(function($q) use ($request){
                $q->where('user_id_sender',$request->user_id)
                    ->where('user_id_receiver',\Auth::user()->id);
                })
            ->get();
        return response()->json($datamail);
    }

    public function SendDataMail(Request $request){
        $mail = new Mail;
        $mail->user_id_sender = \Auth::user()->id;
        $mail->user_id_receiver = $request->input('user_id_receiver');
        $mail->message = $request->input('message');
        $mail->save();
        
        return response()->json($request->all());
    }
}
