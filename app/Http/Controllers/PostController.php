<?php

namespace App\Http\Controllers;

use App\blogCategory;
use App\blogLanguage;
use App\eventCategory;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $languages = blogLanguage::all();
        $eventCategories = eventCategory :: all();
        $blogCategories = blogCategory :: all();

        return view('admin.post',compact('eventCategories','blogCategories','languages'));
    }
}
