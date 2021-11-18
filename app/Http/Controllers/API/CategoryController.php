<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{

    public function index(){
        
        $user = Auth::user();
        $category = Category::where('user_id', '=', $user->id)->get();
        return response()->json([
            'status' => 200,
            'category'=> $category
        ]);
    }

    public function getAllCategoires(){
        $category = Category::all();
        return response()->json([
            'status' => 200,
            'category'=> $category
        ]);
    }

    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'name'=>'required',
            'description'=>'required',
        ]);
        $user = Auth::user();
        
        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'errors' => $validator->getMessageBag()
            ]);
        }else{
            $category = new Category;
            $category->name = $request->input('name');
            $category->description = $request->input('description');
            $category->user_id = $user->id;
            $category->save();
            return response()->json([
                'status' => 200,
                'message' => 'Category Added Successfully'
            ]);
        }
    }

    public function edit($id){
        $category = Category::find($id);
        if($category){
            return response()->json([
                'status' => 200,
                'category' => $category
            ]);
        }else{
            return response()->json([
                'status' => 400,
                'message' => 'No category id found'
            ]);
        }
    }

    public function update(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'name'=>'required',
            'description'=>'required',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 422,
                'errors' => $validator->getMessageBag()
            ]);
        }else{
            $category = Category::find($id);
            if($category){
                $category->name = $request->input('name');
                $category->description = $request->input('description');
                $category->save();
                return response()->json([
                    'status' => 200,
                    'message' => 'Category Added Successfully'
                ]);
            }else{
                return response()->json([
                    'status' => 404,
                    'message' => 'No category id founded '
                ]);
            }
        }
    }

    public function destroy($id){
        $category = Category::find($id);
        if($category){
            try{
                $category->delete();
                //update catalog category == null
                return response()->json([
                    'status' => 200,
                    'message' => 'Category deleted Successfully'
                ]);
            }catch(Exception $e){
                return response()->json([
                    'status' => 400,
                    'message' => 'Something went wrong'
                ]);
            }
            
        }else{
            return response()->json([
                'status' => 400,
                'message' => 'No category id found'
            ]);
        }
    }
}
