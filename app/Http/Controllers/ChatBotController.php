<?php

namespace App\Http\Controllers;

use App\chatbot;
use Illuminate\Http\Request;

use App\Http\Requests;

class ChatBotController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $data = \App\chatbot::all();
        return view('chatbot', compact('data'));
    }

    /**
     * @param Request $re
     * @return string
     */
    public function addQuestion(Request $re)
    {
        /** @var string $question */
        $question = $re->question;
        /** @var string $answer */
        $answer = $re->answer;

        try {
            $data = new \App\chatbot();
            $data->question = $question;
            $data->answer = $answer;
            $data->pageId = $re->pageId;
            $data->save();
            return "success";
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param Request $re
     * @return string
     */
    public function delQuestion(Request $re)
    {
        /** @var int $id */
        $id = $re->id;
        try {
            \App\chatbot::where('id', $id)->delete();
            return "success";
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public static function compile($inputText,$pageId){
        if($pageId == ""){
            return Data::get('exMsg');
        }
        $per =0;
        $reply = "";
        $text = chatbot::where('pageId',$pageId)->get();
        foreach($text as $t){
            similar_text($t->question,$inputText,$per);
            if($per >= Data::get('matchAcc')){
                $reply = $t->answer;
                break;
            }
            else{
                $reply = Data::get('exMsg');
            }
        }
        return $reply;
    }
}
