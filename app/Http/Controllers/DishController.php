<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Dish;
use App\Tag;

use App\Traits\ApiResponser;
use App\Traits\ConsumeInternalService;

class DishController extends Controller
{
    use ApiResponser;
    use ConsumeInternalService;

    private $dish_type;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->baseUri = config('services.recipes.base_uri');
        $this->dish_type = $request->dish_type;
        $this->connection = 'sqlite';

        switch($this->dish_type)
        {
            case 'P':
                $this->connection = 'sqlite_p';
                break;
            case 'B':
                $this->connection = 'sqlite_b';
                break;
            case 'S':
                $this->connection = 'sqlite';
                break;
        }
        // dd($request->dish_type, $this->connection);
    }

    public function index($dish_id_slug, $with_recipe = 1)
    {
        // dd($dish_id_slug, $with_recipe);
        is_numeric($dish_id_slug) ?
        $dish = Dish::on($this->connection)->with('restaurants')->findOrFail($dish_id_slug):
        $dish = Dish::where('slug', $dish_id_slug)->with('restaurants')->first();
        
        if( !$dish )
        {
            $dish = Dish::on('sqlite_b')->where('slug', $dish_id_slug)->first();
        }
        
        if($dish)
        {
            if($dish->recipe_id && $with_recipe)
            {
                // get recipe
                $dish->get()->each->append('recipe');
                
                // dd($dish, 4);

                if($with_recipe == 1)
                {
                    $recipe = $this->performRequest('GET', "/recipe/{$dish->recipe_id}");
                }
                else if($with_recipe == 2) // get recipe without steps
                {
                    $recipe = $this->performRequest('GET', "/recipe/{$dish->recipe_id}/ingredients");
                    // dd($recipe);
                }

                if($recipe) $dish->recipe = json_decode($recipe, true);
            }
        }
        
        return $this->successResponse($dish);
    }

    public function show($dish_type, $dish_id_slug, $with_recipe = 1)
    {
        // get dish
        is_numeric($dish_id_slug) ?
            $dish = Dish::on($this->connection)->with('tags')->findOrFail($dish_id_slug):
            $dish = Dish::on($this->connection)->with('tags')->where('slug', $dish_id_slug)->first();
        
        // dd($dish);
        
        if($dish->recipe_id && $with_recipe)
        {
            // get recipe
            $dish->get()->each->append('recipe');
            
            if($with_recipe == 1)
            {
                $recipe = $this->performRequest('GET', "/recipe/{$dish->recipe_id}");
            }
            else if($with_recipe == 2) // get recipe without steps
            {
                $recipe = $this->performRequest('GET', "/recipe/{$dish->recipe_id}/ingredients");
            }

            if($recipe)
                $dish->recipe = json_decode($recipe, true);
        }

        // if($this->dish_type == 'S')
        // {
        //     $restaurant_ids = Restaurant::where('dish_id', $dish->id)->get()->toArray();
        //     $dish->restaurants = $restaurant_ids;
        // }

        // get tags
        // $dish->get()->each->append('tags');
        // $dish->tags = $dish->tags()->pluck('tag');

        // dd($dish);
        return $this->successResponse(['data' => $dish]);
    }

    public function showByOwner($dish_type, $owner_id, $category = '')
    {
        $dishesByOwner = Dish::on($this->connection)->with('tags')->where([
            ['owner_id', '=', $owner_id],
            // ['category', '=', $category],
        ])->get();
        return $this->successResponse( ['data' => $dishesByOwner] );
    }

    public function getbyids(Request $request)
    {
        $ids = explode(',', $request->ids);
        $dishes = Dish::on($this->connection)->with('restaurants')->find($ids);
        // get recipe
        // $dishes->each->append('recipe');
        // dd($ids, $dishes);


        $dishes = $dishes->each(function($dish){
            $recipe = $this->performRequest('GET', "/recipe/{$dish->recipe_id}/ingredients");
            $recipe = json_decode($recipe, true);
            
            if(!empty($recipe['ingredients']))
                $dish->recipe = $recipe;

            return $dish;
        });

        // dd($dishes->toArray());

        return $this->successResponse($dishes);
    }

    public function store(Request $request, $dish_type)
    {
        // dd($request->all());
        $request['owner_type'] = $dish_type;
        $rules = [
            'slug' => 'required|min:3',
            'bg_name' => 'required|min:3',
        ];
        $this->validate($request, $rules);

        $new_dish = Dish::on($this->connection)->create($request->all());

        if($request->tags)
        {
            foreach(explode(',', $request->tags) as $tag)
            {
                Tag::on($this->connection)->create(['dish_id' => $new_dish->id, 'tag' => $tag]);
            }
        }

        // Create a Recipe entry /* return $recipe_id */
        if(!$request->steps) $request['steps'] = '[]';
        $recipe = $this->performRequest('POST', '/recipes', ['steps' => $request['steps']]);
        $recipe = json_decode($recipe, true);
        $recipe_id = $recipe['id'];

        if($request->ingredients)
        {
            $ingredients = json_decode($request->ingredients, true);
            foreach($ingredients as $ingredient)
            {
                $this->performRequest('POST', '/recipe/'.$recipe_id.'/ingredients', [
                    'ingredient_id' => $ingredient['ingredient_id'],
                    'recipe_id' => $recipe_id,
                    'unit' => $ingredient['unit'],
                    'quantity' => $ingredient['quantity'],
                ]);
            }
        }

        Dish::on($this->connection)->find($new_dish->id)->update(['recipe_id' => $recipe_id]);

        return $this->successResponse($new_dish, Response::HTTP_CREATED);
    }

    public function update(Request $request, $dish_type, $dish_id)
    {
        // $rules = [
        //     // 'slug' => 'max:255',
        //     'bg_name' => 'max:255',
        //     'en_name' => 'max:255',
        // ];
        // $this->validate($request, $rules);

        $dish = Dish::on($this->connection)->findOrFail($dish_id);

        if( $dish_type != 'S' )
        {
            ($request->public == 'on') ? $request['public'] = true : $request['public'] = false;
        }
        
        if( !empty($request->all()) )
        {
            $dish->fill($request->all());
            // if($dish->isClean())
            // {
            //     return $this->errorResponse('At least one value must change', Response::HTTP_UNPROCESSABLE_ENTITY);
            //     return $this->successResponse($dish);
            // }
            $dish->save();

            // dd($dish->recipe_id, $request->steps);
            // update recipe
            if( $dish->recipe_id )
            {
                if( is_null($request->steps) ) $request['steps'] = '[]';
                $recipe = $this->performRequest('GET', '/recipe/'.$dish->recipe_id);
                if($recipe)
                {
                    $this->performRequest('PUT', '/recipe/'.$dish->recipe_id, ['steps' => $request->steps]);
                }
                else
                {
                    $recipe = $this->performRequest('POST', '/recipes', ['steps' => $request->steps]);
                    $dish->update(['recipe_id' => $recipe]);
                }

                // update ingredients
                if($request->ingredients)
                {
                    $old_ingredients = $this->performRequest('GET', "/recipe/{$dish->recipe_id}/ingredients");
                    $old_ingredients = json_decode($old_ingredients, true)['ingredients'];
                    $requested_ingredients = json_decode($request->ingredients, true);
                    $old_ingredients_IDs = collect($old_ingredients)->pluck('id')->toArray();
                    $update_ingredients_IDs = collect($requested_ingredients)->pluck('id')->toArray();
                    $new_ingredients = array_filter(collect($requested_ingredients)->pluck('ingredient_id')->toArray());
                    $remove_IDs = array_diff($old_ingredients_IDs, array_keys(array_flip(array_filter($update_ingredients_IDs))));
                    foreach($remove_IDs as $id)
                    {
                        $this->performRequest('DELETE', '/ingredient/'.$id);
                    }
                    $update_ingredients_IDs = array_keys(array_flip(array_filter($update_ingredients_IDs)));
                    foreach($update_ingredients_IDs as $id)
                    {
                        // dd($id);
                        $updated = $this->performRequest('PATCH', '/ingredient/'.$id, collect($requested_ingredients)->where('id', $id)->first());
                        // dd($updated);
                    }
                    foreach($new_ingredients as $ingredient_id)
                    {
                        $ingredient = collect($requested_ingredients)->where('ingredient_id', $ingredient_id)->first();
                        $this->performRequest('POST', '/recipe/'.$dish->recipe_id.'/ingredients', [
                            'ingredient_id' => $ingredient['ingredient_id'],
                            'recipe_id' => $dish->recipe_id,
                            'unit' => $ingredient['unit'],
                            'quantity' => $ingredient['quantity'],
                        ]);
                    }
                }
            }
        }
        return $this->successResponse(['data' => $dish]);
    }

    public function destroy($dish_type, $dish_id)
    {
        $dish_id = (int)$dish_id;
        $dish = Dish::on($this->connection)->findOrFail($dish_id);
        if($dish->toArray()['recipe_id'])
        {
            // delete recipe
            $recipe = $this->performRequest('DELETE', "/recipe/{$dish->toArray()['recipe_id']}");
            // delete ingredients
            $ingredients = $this->performRequest('DELETE', "/recipe/{$dish->toArray()['recipe_id']}/ingredients");
        }
        
        if($dish->get()->isNotEmpty())
        {
            $dish->delete();
        }
        return $this->successResponse(['data' => $dish]);
    }

    public function adddishtorestaurantid(Request $request)
    {
        // dd($request->all());
        $restaurant_id = $request->restaurant_id;
        $dish_id = $request->dish_id;

        $new_entry = \App\Restaurant::where([
            'restaurant_id' => $restaurant_id,
            'dish_id' => $dish_id
        ])->get();

        if($new_entry->isEmpty())
        {
            $new_entry = \App\Restaurant::create($request->all());
        }
        
        return $this->successResponse(['data' => $new_entry]);
    }

    public function removedishtorestaurantid(Request $request)
    {
        $restaurant_id = $request->restaurant_id;
        $dish_id = $request->dish_id;

        $entries = \App\Restaurant::where([
            'restaurant_id' => $restaurant_id,
            'dish_id' => $dish_id
        ]);

        if($entries->get()->isNotEmpty())
        {
            $entries->delete();
        }

        return $this->successResponse(['data' => $new_entry]);
    }
}
