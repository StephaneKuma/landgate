<?php

namespace App\Http\Controllers\Agent;

use Carbon\Carbon;
use App\Mail\Contact;
use App\Models\Message;
use App\Models\Property;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function index()
    {
        $properties = Property::latest()->where('agent_id', Auth::id())->take(5)->get();
        $propertiesTotal = Property::latest()->where('agent_id', Auth::id())->count();

        $messages = Message::latest()->where('agent_id', Auth::id())->take(5)->get();
        $messagesTotal = Message::latest()->where('agent_id', Auth::id())->count();

        return view('agent.dashboard', compact('properties', 'propertiesTotal', 'messages', 'messagesTotal'));
    }

    public function profile()
    {
        $profile = Auth::user();

        return view('agent.profile', compact('profile'));
    }

    public function profileUpdate()
    {
        $request->validate([
            'name'      => 'required',
            'username'  => 'required',
            'email'     => 'required|email',
            'image'     => 'image|mimes:jpeg,jpg,png',
            'about'     => 'max:250'
        ]);
        
        $user = User::find(Auth::id());

        $image = $request->file('image');
        $slug  = str_slug($request->name);

        if(isset($image)){
            $currentDate = Carbon::now()->toDateString();
            $imagename = $slug.'-agent-'.Auth::id().'-'.$currentDate.'.'.$image->getClientOriginalExtension();

            if(!Storage::disk('public')->exists('users')){
                Storage::disk('public')->makeDirectory('users');
            }
            if(Storage::disk('public')->exists('users/'.$user->image) && $user->image != 'default.png' ){
                Storage::disk('public')->delete('users/'.$user->image);
            }
            $userimage = Image::make($image)->stream();
            Storage::disk('public')->put('users/'.$imagename, $userimage);
        }

        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->image = $imagename;
        $user->about = $request->about;

        $user->save();

        return back();
    }

    public function changePassword()
    {
        return view('agent.change_password');
    }

    public function changePasswordUpdate()
    {
        if (!(Hash::check($request->get('currentpassword'), Auth::user()->password))) {

            Toastr::error('message', 'Your current password does not matches with the password you provided! Please try again.');
            return redirect()->back();
        }
        if(strcmp($request->get('currentpassword'), $request->get('newpassword')) == 0){

            Toastr::error('message', 'New Password cannot be same as your current password! Please choose a different password.');
            return redirect()->back();
        }

        $this->validate($request, [
            'currentpassword' => 'required',
            'newpassword' => 'required|string|min:6|confirmed',
        ]);

        $user = Auth::user();
        $user->password = bcrypt($request->get('newpassword'));
        $user->save();

        Toastr::success('message', 'Password changed successfully.');
        return redirect()->back();
    }

    public function message()
    {
        $messages = Message::latest()->where('agent_id', Auth::id())->paginate(10);

        return view('agent.messages.index', compact('messages'));
    }

    public function messageRead($id)
    {
        $message = Message::findOrFail($id);

        return view('agent.messages.read', compact('message'));
    }

    public function messageReplay($id)
    {
        $message = Message::findOrFail($id);

        return view('agent.messages.replay', compact('message'));
    }

    public function messageSend(Request $request)
    {
        $request->validate([
            'agent_id'  => 'required',
            'user_id'   => 'required',
            'name'      => 'required',
            'email'     => 'required',
            'phone'     => 'required',
            'message'   => 'required'
        ]);

        Message::create($request->all());

        Toastr::success('message', 'Message send successfully.');
        return back();

    }

    public function messageReadUnread(Request $request)
    {
        $status = $request->status;
        $msgId  = $request->messageid;

        if($status) {
            $status = 0;
        } else {
            $status = 1;
        }

        $message = Message::findOrFail($msgId);
        $message->status = $status;
        $message->save();

        return redirect()->route('agent.message');
    }

    public function messageDelete($id)
    {
        $message = Message::findOrFail($id);
        $message->delete();

        Toastr::success('message', 'Message deleted successfully.');
        return back();
    }


    public function contactMail(Request $request)
    {
        $message  = $request->message;
        $name     = $request->name;
        $mailfrom = $request->mailfrom;

        Mail::to($request->email)->send(new Contact($message,$name,$mailfrom));

        Toastr::success('message', 'Mail send successfully.');
        return back();
    }
}
