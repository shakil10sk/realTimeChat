<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mesage;
use Illuminate\Support\Facades\Auth;

use App\Events\SendMesage;

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
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function chat(){
        return view('chat');
    }


    public function message(){
        return Mesage::with('user')->get();
    }

    public function messageStore(Request $request){
        $user = Auth::user();

        $messge = $user->message()->create([
            'message' => $request->message,
        ]);

        broadcast(new SendMesage($user,$messge))->toOthers();

        return 'Message sent';
    }
}
