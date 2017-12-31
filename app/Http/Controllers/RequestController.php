<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\WordpressPost;

class RequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function laravelRequest()
    {
        $results = WordpressPost::where('post_status', 'publish')
        ->select('post_title', 'post_content')
        ->get();
        dd($results);
        foreach ($results as $result) {
            echo $result->post_title;
        }
    }
}
