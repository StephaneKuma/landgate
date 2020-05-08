<?php

namespace App\Http\Controllers\User;

use App\User;
use Carbon\Carbon;
use App\Models\Message;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;
use App\Repositories\CommentRepository;
use App\Repositories\MessageRepository;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    private $commentRepository;
    private $messageRepository;

    public function __construct(CommentRepository $commentRepository, MessageRepository $messageRepository)
    {
        $this->commentRepository = $commentRepository;
        $this->messageRepository = $messageRepository;
    }

    public function index()
    {
        $comments = $this->commentRepository->getLatest(Auth::id(), 10);
        $commentsCount = $this->commentRepository->countByUserId(Auth::id());
        return view('user.dashboard', compact('comments', 'commentsCount'));
    }

    public function profile()
    {
        $profile = Auth::user();
        return view('user.profile', compact('profile'));
    }

    private function validator()
    {
        return request()->validate([
            'name'      => 'required',
            'username'  => 'required',
            'email'     => 'required|email',
            'image'     => 'sometimes|image|mimes:jpeg,jpg,png',
            'about'     => 'sometimes|max:250'
        ]);
    }

    public function profileUpdate(Request $request)
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
            $imagename = $slug.'-user-'.Auth::id().'-'.$currentDate.'.'.$image->getClientOriginalExtension();

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
        return view('user.change_password');
    }

    public function changePasswordUpdate(Request $request)
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
        $messages = $this->messageRepository->getLatest(Auth::id(), 10);

        return view('user.messages.index',compact('messages'));
    }

    public function messageRead($id)
    {
        $message = $this->messageRepository->getById($id);

        return view('user.messages.read',compact('message'));
    }

    public function messageReplay($id)
    {
        $message = $this->messageRepository->getById($id);

        return view('user.messages.replay',compact('message'));
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
        $msgid  = $request->messageid;

        if($status){
            $status = 0;
        }else{
            $status = 1;
        }

        $message = $this->messageRepository->getById($msgid);
        $message->status = $status;
        $message->save();

        return redirect()->route('user.message');
    }

    public function messageDelete($id)
    {
        $this->messageRepository->destroy($id);

        Toastr::success('message', 'Message deleted successfully.');
        return back();
    }
}
