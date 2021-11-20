<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\ChatPost;

class ChatPostController extends Controller
{
    public function store(Request $request){
        $user = Auth::user();
        $user->id;

        $validator = Validator::make($request->all(), [
            'content'=>'required',
        ]);
        
        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'errors' => $validator->getMessageBag()
            ]);
        }else{
            $chatPost = new ChatPost;
            $chatPost->content = $request->input('content');
            $chatPost->user_id = $user->id;
            $chatPost->chat_id = $request->input('chat_id');
            $chatPost->save();
            return response()->json([
                'status' => 200,
                'message' => 'Post Added Successfully'
            ]);
        }
    }
}
