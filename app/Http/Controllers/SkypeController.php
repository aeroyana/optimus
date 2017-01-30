<?php

namespace App\Http\Controllers;

use App\OptLog;
use App\Phones;
use App\Setting;
use Illuminate\Http\Request;

use App\Http\Requests;
use Prappo\skype;

class SkypeController extends Controller
{
    //
    public function index(){
        if (Setting::where('field', 'skypePass')->exists()) {
            foreach (Setting::where('field', 'skypePass')->get() as $d) {
                if ($d->value == "") {
                    return redirect('/settings');
                }
            }
        } else {
            return redirect('/settings');
        }
        $skype = new \App\Http\Controllers\Skype(Data::get('skypeUser'),Data::get('skypePass'));
        $profile = $skype->readMyProfile();
        $contacts = $skype->getContactsList();
        return view('skype',compact('profile','contacts'));
    }

    public function skypeUser($user){
        $skype = new \App\Http\Controllers\Skype(Data::get('skypeUser'),Data::get('skypePass'));
        $profile = $skype->readProfile(array($user));
        $messages = $skype->getMessagesList($user);

        $userId = $user;

        return view('skypechat',compact('profile','userId','messages'));


    }


    public function getMessage($user){
        $skype = new \App\Http\Controllers\Skype(Data::get('skypeUser'),Data::get('skypePass'));
        $messages = $skype->getMessagesList($user);
        $userId = $user;
        return view('skypechatajax',compact('userId','messages'));
    }

    public function sendMessage(Request $re){
        $userId = $re->userId;
        $message = $re->message;

        $skype = new \App\Http\Controllers\Skype(Data::get('skypeUser'),Data::get('skypePass'));
        try{
            $skype->sm($userId,$message);
            return "success";
        }
        catch (\Exception $e){
            return $e->getMessage();
        }

    }

    public function sendRequest(Request $re){
        $message = ($re->reqMessage == "")?"Hello, I would like to add you to my contacts.":$re->reqMessage;
        $skype = new \App\Http\Controllers\Skype(Data::get('skypeUser'),Data::get('skypePass'));
        try{
            $skype->addContact($re->userName,$message);
            return "success";
        }
        catch (\Exception $e){
            return $e->getMessage();
        }
    }

    public function massSend(Request $re){
        $skype = new \App\Http\Controllers\Skype(Data::get('skypeUser'),Data::get('skypePass'));
        try{

            $lists = $skype->getContactsList();
            foreach ($lists as $listno => $list) {
                try{
                    $skype->sm($list['id'],$re->message);
//                    echo "<span class=\"pull-right badge bg-green\">Message sent to {$list['id']}</span>";
                }
                catch (\Exception $d){
//                    echo "<span class=\"pull-right badge bg-red\">Can't sent to {$list['id']}</span>";
//                    echo $d->getMessage();
                }
            }
            return "success";

        }
        catch (\Exception $e){
            return $e->getMessage();
        }
    }

    public static function massSendS($postId,$message,$type){
        $skype = new \App\Http\Controllers\Skype(Data::get('skypeUser'),Data::get('skypePass'));
        $log = new OptLog();
        try{
            $lists = $skype->getContactsList();
            foreach ($lists as $listno => $list) {
                try{
                    $skype->sm($list['id'],$message);

                }
                catch (\Exception $d){
                }
            }

            $log->postId = $postId;
            $log->status = "success";
            $log->from = "Wordpress";
            $log->type = $type;

        }
        catch (\Exception $e){
            $log->postId = $postId;
            $log->status = "success";
            $log->from = "error";
            $log->type = $type;
        }
        $log->save();
    }

    public function collectPhone(){
        $skype = new \App\Http\Controllers\Skype(Data::get('skypeUser'),Data::get('skypePass'));
        $lists = $skype->getContactsList();
        try {
            foreach ($lists as $listno => $list) {

                if (isset($list['phones'])) {
                    foreach ($list['phones'] as $phones) {
                        if(!Phones::where('phone',$phones['number'])->exists()){
                            $phone = new Phones();
                            $phone->username = $list['id'];
                            $phone->phone = $phones['number'];
                            $phone->save();
                        }

                    }
                }

            }
            return "Done";
        }
        catch (\Exception $e){
            return $e->getMessage();
        }
    }

    public function showPhones(){
        $data = Phones::all();
        return view('skypephones',compact('data'));
    }

    public function del(Request $re){

        try{
            Phones::where('id',$re->id)->delete();
            return "success";
        }
        catch (\Exception $e){
            return $e->getMessage();
        }
    }

    public function delAll(){
        try{
            Phones::truncate();
            return "success";
        }
        catch (\Exception $e){
            return $e->getMessage();
        }
    }
}
