<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Dish;

use App\Traits\ApiResponser;

class SystemController extends Controller
{
    use ApiResponser;
    
    public function __construct(Request $request)
    {
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

        // dd($this->dish_type, $this->connection);
    }

    public function all()
    {
        $dishesByType = Dish::on($this->connection)->get();
        dd($dishesByType);
        return $this->successResponse( $dishesByType );
    }

    public function allrestaurantdishes()
    {
        $entries = \App\Restaurant::all();
        // dd($entries);
        return $this->successResponse( $entries );
    }

}
