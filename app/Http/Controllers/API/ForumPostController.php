<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Forum;
use App\Models\ForumPost;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;


class ForumPostController extends Controller
{
    
    public function view($forumId){
        $forum = Forum::find($forumId);
        if($forum){
            $forumPost = ForumPost::where('forum_id', '=', $forum->id)->get();
            foreach($forumPost as $post){
                $userName = User::where('id', '=', $post->user_id)->get();
                $post->user_name = $userName[0]->name;
            }

            if($forumPost){
                return response()->json([
                    'status' => 200,
                    'forumPost' => $forumPost,
                    'forum' => $forum
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
                'message' => 'No forum id founded '
            ]);
        }
    }

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
            $forumPost = new ForumPost;
            $forumPost->content = $request->input('content');
            $forumPost->user_id = $user->id;
            $forumPost->forum_id = $request->input('forum_id');
            $forumPost->save();
            return response()->json([
                'status' => 200,
                'message' => 'Forum Added Successfully'
            ]);
        }
    }

    public function destroy($id){
        $post = ForumPost::find($id);
        if($post){
            $post->delete();
            return response()->json([
                'status' => 200,
                'message' => 'Post deleted Successfully'
            ]);
        }else{
            return response()->json([
                'status' => 400,
                'message' => 'No post id found'
            ]);
        }
    }

    public function destroyAllPostsFromForumTopic($forumTopicId){
        if($forumTopicId){
            ForumPost::where('forum_id', $forumTopicId)->delete();
            return response()->json([
                'status' => 200,
                'message' => 'Forum posts deleted Successfully'
            ]);
        }else{
            return response()->json([
                'status' => 400,
                'message' => 'No forum posts id found'
            ]);
        }
    }
}
