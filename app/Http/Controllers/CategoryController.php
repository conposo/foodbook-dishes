<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Dish;
use App\Categories;

use App\Traits\ApiResponser;

class CategoryController extends Controller
{
    use ApiResponser;

    public function index(Request $request, $category)
    {

        if($request->tags) $tags = explode(',', $request->tags);
        // dd($request->tags, $tags);

        $dishes = Dish::where('category', $category)->with('tags')->get()->map(function($item){
            $newitem = [];
            $tags = $item->tags->map(function($tag) {
                return $tag->tag;
            });
            
            $newitem['id'] = $item->id;
            $newitem['category'] = $item->category;
            $newitem['slug'] = $item->slug;
            $newitem['bg_name'] = $item->bg_name;
            $newitem['description'] = $item->description;
            $newitem['recipe_id'] = $item->recipe_id;
            $newitem['tags'] = $tags->toArray();
            return $newitem;
        });

        if($request->tags):
        $dishes = Dish::where('category', $category)->get()->map(function($item) use ($tags){
            if($tags)
            {
                if( empty(array_diff($tags, $item->tags->pluck('tag')->toArray())) )
                    return $item;
            }
            else return $item;
        });
        endif;
        // dd( $dishes );
        
        return $this->successResponse($dishes);
    }

    public function get($user_type_id)
    {
        $categories = Categories::where([
            ['user_type_id', '=', $user_type_id]
        ])->get();

        return $this->successResponse(['data' => $categories], Response::HTTP_OK);
    }

    public function store(Request $request, $user_type_id)
    {
        $categories[] = Categories::create([
            'user_type_id' => $user_type_id,
            'slug' => strtolower($request->slug),
            'en_name' => $request->en_name,
            'bg_name' => $request->bg_name,
        ]);

        return $this->successResponse($categories, Response::HTTP_CREATED);
    }

    public function destroy(Request $request, $user_type_id)
    {
        $category = Categories::where([
            ['user_type_id', '=', $user_type_id],
            ['slug', '=', mb_strtolower($request['slug'])]
        ])->get();

        if($category)
        {
            $category->first()->delete();
            // To-Do
            // edit dishes => set to uncategorized
        }

        $categories = Categories::all();
        return $this->successResponse($categories, Response::HTTP_OK);
    }
    
    public function update(Request $request, $slug_or_id)
    {
        //
    }
    
    public function all($user_type_id)
    {
        $categories = Categories::all();
        return $this->successResponse($categories, Response::HTTP_OK);
    }
}
