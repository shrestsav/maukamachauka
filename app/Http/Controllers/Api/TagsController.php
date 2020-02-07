<?php

namespace App\Http\Controllers\Api;

use App\Category;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TagsController extends Controller
{
    public function index()
    {
    	$categories = Category::doesntHave('tagsUsers')->paginate(10);

    	return response()->json($categories);
    }
}
