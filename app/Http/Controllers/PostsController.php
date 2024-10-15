<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostsController extends Controller
{
    public function getAll(){
        return Post::all();
    }
    
    public function getSingle($id){
        return Post::find($id);
    }

    public function storeProject(Request $request){
        $project = Post::create($request->all());
        return response()->json($project, 201); 
        
    }

    public function updateProject(Request $request,){
        $project = Post::find($request->id);
        $project->update($request->all());
        return response()->json($project, 200);

    }

    public function deleteProject($id){
        $project = Post::find($id);
        $project->delete(); 
        return response()->json(null, 204);
    }

}
