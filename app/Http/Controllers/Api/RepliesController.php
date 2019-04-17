<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Topic;
use App\Models\Reply;
use App\Http\Requests\Api\ReplyRequest;
use App\Transformers\ReplyTransformer;

class RepliesController extends Controller
{
    public function store(Topic $topic, ReplyRequest $request, Reply $reply)
    {
    	$reply->content = $request->content;
    	$reply->user_id = $this->user()->id;
    	$reply->topic_id = $topic->id;
    	$reply->save();

    	return $this->response->item($reply, new ReplyTransformer())
    		->setStatusCode(201);
    }

    public function destroy(Topic $topic, Reply $reply)
    {
        if ($reply->topic_id != $topic->id) {
            return $this->response->errorBadRequest();
        }

        $this->authorize('destroy', $reply);
        $reply->delete();

        return $this->response->noContent();
    }    
}
