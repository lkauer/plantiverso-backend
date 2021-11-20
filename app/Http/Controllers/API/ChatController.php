<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\User;
use App\Models\ChatPost;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index(){
        $userAuth = Auth::user();
        $chatConversation = Chat::where('user_a_id', '=', $userAuth->id)
        ->orwhere('user_b_id', '=', $userAuth->id)->get(); 
        foreach($chatConversation as $chat){
            if($chat->user_a_id != $userAuth->id){
                $user = User::find($chat->user_a_id);
                
            }else if($chat->user_b_id != $userAuth->id){
                $user = User::find($chat->user_b_id);
            }
            $chat->user_id = $user->id;
            $chat->username = $user->name;
        }

        return response()->json([
            'status' => 200,
            'chat'=> $chatConversation
        ]);
    }

    public function store(Request $request){
        
        $validator = Validator::make($request->all(), [
            'user_id'=>'required',
            'own_user_id'=>'required',
        ]);
        
        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'errors' => $validator->getMessageBag()
            ]);
        }else{

            $definedChats = Chat::where([
                ['user_a_id', '=', $request->input('own_user_id')],
                ['user_b_id', '=', $request->input('user_id')],
                ])
            ->orWhere([
                ['user_b_id', '=', $request->input('own_user_id')],
                ['user_a_id', '=', $request->input('user_id')],
            ])->get();

            if(!isset($definedChats[0])){
                $chat = new Chat;
                $chat->user_a_id = $request->input('own_user_id');
                $chat->user_b_id = $request->input('user_id');
                $chat->save();
                return response()->json([
                    'status' => 200,
                    'message' => 'Chat defined Successfully'
                ]);
            }else{
                return response()->json([
                    'status' => 201,
                    'message' => 'Chat already exists'
                ]);
            }
            
        }
    }

    public function view($chatId){
        $chat = Chat::find($chatId);
        if($chat){
            $chatPost = ChatPost::where('chat_id', '=', $chat->id)->get();
            foreach($chatPost as $post){
                $userName = User::where('id', '=', $post->user_id)->get();
                $post->user_name = $userName[0]->name;
            }
            $chatUserA = $user = User::find($chat->user_a_id);
            $chatUserB = $user = User::find($chat->user_b_id);

            $chat->chatUserA = $chatUserA->name;
            $chat->chatUserB = $chatUserB->name;
            
            if($chatPost){
                return response()->json([
                    'status' => 200,
                    'chatPost' => $chatPost,
                    'chat' => $chat
                ]);    
            }else{
                return response()->json([
                    'status' => 404,
                    'message' => 'Something went wrong '
                ]);    
            }
            
        }else{
            return response()->json([
                'status' => 404,
                'message' => 'No chat id founded '
            ]);
        }
    }
}
