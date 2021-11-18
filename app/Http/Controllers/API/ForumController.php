<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Forum;
use App\Http\Controllers\API\ForumPostController;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ForumController extends Controller
{
    public function index(){
        $user = Auth::user();
        $forum = Forum::where('user_id', '=', $user->id)->get();
        return response()->json([
            'status' => 200,
            'forum'=> $forum
        ]);
    }

    public function general(){
        $forum = Forum::all();
        return response()->json([
            'status' => 200,
            'forum'=> $forum
        ]);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'title'=>'required',
            'description'=>'required',
        ]);
        $user = Auth::user();
        
        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'errors' => $validator->getMessageBag()
            ]);
        }else{
            $forum = new Forum;
            $forum->title = $request->input('title');
            $forum->description = $request->input('description');
            $forum->user_id = $user->id;
            $forum->save();
            return response()->json([
                'status' => 200,
                'message' => 'Forum Added Successfully'
            ]);
        }
    }

    public function edit($id){
        $forum = Forum::find($id);
        if($forum){
            return response()->json([
                'status' => 200,
                'forum' => $forum
            ]);
        }else{
            return response()->json([
                'status' => 400,
                'message' => 'No forum topic id found'
            ]);
        }
    }

    public function update(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'title'=>'required',
            'description'=>'required',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 422,
                'errors' => $validator->getMessageBag()
            ]);
        }else{
            $forum = Forum::find($id);
            if($forum){
                $forum->title = $request->input('title');
                $forum->description = $request->input('description');
                $forum->save();
                return response()->json([
                    'status' => 200,
                    'message' => 'Forum Added Successfully'
                ]);
            }else{
                return response()->json([
                    'status' => 404,
                    'message' => 'No forum id founded '
                ]);
            }
        }
    }

    public function destroy($id){
        $forum = Forum::find($id);
        if($forum){
            try{
                $forumPosts = new ForumPostController();
                $forumPosts->destroyAllPostsFromForumTopic($id);
                $forum->delete();
                return response()->json([
                    'status' => 200,
                    'message' => 'Forum deleted Successfully'
                ]);
            }catch(Exception $e){
                return response()->json([
                    'status' => 400,
                    'message' => $e->getMessage()
                ]);
            }   
        }
        return response()->json([
            'status' => 400,
            'message' => 'No forum id found'
        ]);
    }
}
