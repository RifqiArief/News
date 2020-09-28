<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function comment(Request $request)
    {
        if ($request->user()->id_role != 1){
            $comment = new Comment;
            $comment->comment = $request->comment;
            $comment->user()->associate($request->user());

            $news = News::where('id_news','=',$request->id_news)->first();
            $news->comments()->save($comment);

            $res = array(
                'code'      => 0,
                'message'   => 'sukses',
            );
            return $res;
        } else{
            $res = array(
                'code'      => 99,
                'message'   => 'anda tidak diizinkan',
            );
            return response()->json($res, 200);
        }
    }

    public function reply(Request $request)
    {
        if ($request->user()->id_role != 1){

            $reply = new Comment();
            $reply->comment = $request->comment;
            $reply->user()->associate($request->user());
            $reply->parent_id = $request->id_news;

            $news = News::where('id_news','=',$request->id_news)->first(); 
            $news->comments()->save($reply);

            $res = array(
                'code'      => 0,
                'message'   => 'sukses',
            );
            return $res;
        } else{
            $res = array(
                'code'      => 99,
                'message'   => 'anda tidak diizinkan',
            );
            return response()->json($res, 200);
        }
    }
}
