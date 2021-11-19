<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Catalog;
use App\Models\Category;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Exception;

class CatalogController extends Controller
{
    public function index(){
        $user = Auth::user();
        $catalog = Catalog::where('user_id', '=', $user->id)->get();
        if($catalog){
            foreach( $catalog as $cat){
                if($cat->category_id){
                    $category = Category::where('id', '=', $cat->category_id)->get(); 
                    if(isset($category[0])){
                        $cat->category = $category[0]->name;
                    }else{
                        $cat->category = "Nenhuma"; 
                    }
                    
                }else{
                    $cat->category = "Nenhuma"; 
                }
            }
        }
        return response()->json([
            'status' => 200,
            'catalog'=> $catalog
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
            $catalog = new Catalog;
            $catalog->name = $request->input('name');
            $catalog->description = $request->input('description');
            if($request->hasFile('image')){
                $file = $request->file('image');
                $extension = $file->getClientOriginalExtension();
                $filename = time().'.'.$extension;
                $file->move('uploads/catalog/', $filename);
                $catalog->image = 'uploads/catalog/'. $filename;

            }
            $catalog->category_id = ($request->input('category'))? $request->input('category') : 0;
            $catalog->user_id = $user->id;
            $catalog->save();
            return response()->json([
                'status' => 200,
                'message' => 'Catalog item Added Successfully'
            ]);
        }
    }

    public function edit($id){
        $catalog = Catalog::find($id);
        $category = Category::all();
        if($catalog){
            return response()->json([
                'status' => 200,
                'catalog' => $catalog,
                'category' => $category
            ]);
        }else{
            return response()->json([
                'status' => 400,
                'message' => 'No catalog id found'
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
            $catalog = Catalog::find($id);
            if($catalog){
                $catalog->name = $request->input('name');
                $catalog->description = $request->input('description');
                if($request->hasFile('image')){
                    $file = $request->file('image');
                    $extension = $file->getClientOriginalExtension();
                    $filename = time().'.'.$extension;
                    $file->move('uploads/catalog/', $filename);
                    $catalog->image = 'uploads/catalog/'. $filename;
    
                }
                $catalog->category_id = ($request->input('category'))? $request->input('category') : 0;
                $catalog->save();
                return response()->json([
                    'status' => 200,
                    'message' => 'Catalog updated Successfully'
                ]);
            }else{
                return response()->json([
                    'status' => 404,
                    'message' => 'No catalog id founded '
                ]);
            }
        }
    }

    public function destroy($id){
        $catalog = Catalog::find($id);
        if($catalog){
            try{
                $catalog->delete();
                return response()->json([
                    'status' => 200,
                    'message' => 'Catalog item deleted Successfully'
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
            'message' => 'No catalog item id found'
        ]);
    }

    public function getAllCatalogItens(){
        $catalog = Catalog::all();
        if($catalog){
            foreach( $catalog as $cat){
                if($cat->category_id){
                    $category = Category::where('id', '=', $cat->category_id)->get(); 
                    if(isset($category[0])){
                        $cat->category = $category[0]->name;
                    }else{
                        $cat->category = "Nenhuma"; 
                    }
                    
                }else{
                    $cat->category = "Nenhuma"; 
                }
            }
        }
        return response()->json([
            'status' => 200,
            'catalog'=> $catalog
        ]);        
    }

    public function generalSearch($searchContent){
        $catalog = Catalog::where('description', 'like', '%' . $searchContent. '%')->get();
        if($catalog){
            foreach( $catalog as $cat){
                if($cat->category_id){
                    $category = Category::where('id', '=', $cat->category_id)->get(); 
                    if(isset($category[0])){
                        $cat->category = $category[0]->name;
                    }else{
                        $cat->category = "Nenhuma"; 
                    }
                    
                }else{
                    $cat->category = "Nenhuma"; 
                }
            }
        }
        return response()->json([
            'status' => 200,
            'catalog'=> $catalog
        ]);        
    }

}
