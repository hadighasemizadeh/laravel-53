<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Article;
use Auth;

class ArticleController extends Controller
{
//    public function __construct()
//    {
//        $this->middleware('auth:api', ['except' => ['index','show']]);
//    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
            header('Access-Control-Allow-Origin:*');
        $articles = Article::all();

        foreach ($articles as $article)
        {
            $article->view_article = [
                'href'=>'v1/article/'. $article->id,
                'method'=>'GET'
            ];
        }
        $response = [
            'msg'=> 'List of all articles.',
            'articles' => $articles
        ];
        return response()->json($response,200);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        header('Access-Control-Allow-Origin:*');
        $validation = Article::validate($request->all());
        if ($validation->fails()) {
            return $validation->errors()->all();
        }

        $title = $request->input('title');
        $description = $request->input('description');
        $time = $request->input('time');
        //$user_id = Auth::user()->id;
        $image = $request->file('image');

        $realName = date('Y-m-d-H:i:s')."-".$image->getClientOriginalName();
        $input = md5($realName).time().'.'.$image->getClientOriginalExtension();
        $destinationPath = public_path('images');
        $image->move($destinationPath, $input);

        $article = new Article([
            'title' => $title,
            'description' =>  $description,
            'time' => $time,
            'user_id' =>1,
            'image' =>$input,
        ]);

        if ($article->save()) {
            //$article->users()->attach($user_id);
            $article->view_article = [
                'href' => 'v1/article/' . $article->id,
                'method' => 'GET'
            ];
            $message = [
                'msg' => 'Article created',
                'article' => $article
            ];
            return response()->json($message, 201);
        }

        $response = [
            'msg'=> 'Article Created successfully.',
            'article'=>$article
        ];

        return response()->json($response,201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        header('Access-Control-Allow-Origin:*');
        $article = Article::with('users')->where('id', $id)->firstOrFail();
        $article->view_article = [
            'href' => 'v1/article',
            'method' => 'GET'
        ];

        $response = [
            'msg' => 'Article information',
            'article' => $article
        ];
        return response()->json($response, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        header('Access-Control-Allow-Origin:*');
        $validation = Article::validate($request->all());
        if ($validation->fails()) {
            return $validation->errors()->all();
        }

        $title = $request->input('title');
        $description = $request->input('description');
        $time = $request->input('time');
//      $user_id = $request->input('user_id');
        $image = $request->file('image');

        $realName = date('Y-m-d-H:i:s')."-".$image->getClientOriginalName();
        $input = md5($realName).time().'.'.$image->getClientOriginalExtension();
        $destinationPath = public_path('images');
        $image->move($destinationPath, $input);
        $article = Article::with('users')->findOrFail($id);

//        if (!$article->users()->where('users.id', $user_id)->first()) {
//            return response()->json(['msg' => 'user not registered this article, update not successful'], 401);
//        };

        $article->time = Carbon::createFromFormat('YmdHie', $time);
        $article->title = $title;
        $article->description = $description;
        $article->image = $image;
        $article->update();

        if (!$article->update()) {
            return response()->json(['msg' => 'Error during updating occered'], 404);
        }

        $article->view_article = [
            'href' => 'v1/article/' . $article->id,
            'method' => 'PUT'
        ];

        $response = [
            'msg' => 'article updated',
            '$article' => $article
        ];
        return response()->json($response, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        header('Access-Control-Allow-Origin:*');
        $article= Article::findOrFail($id);
        $article->delete();

        $response = [
            'msg'=> 'Article deleted successfully.',
            'create' => [
                'href' =>'v1/article',
                'method' =>'POST',
                'params' => 'title,description,time'
            ]
        ];
        return response()->json($response,200);
    }
}
