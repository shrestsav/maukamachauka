<?php

namespace App\Http\Controllers\Api;

use App\Category;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TagsController extends Controller
{
    public function index()
    {
    	$categories = Category::get()->makeHidden('tags_users')->makeVisible('subscribed_status');

    	return $categories;
    }
}
