<?php

namespace App\Http\Controllers\API\Post;

use App\Http\Controllers\BaseController;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Http\Resources\PostCollection;
use App\Http\Resources\Post as PostResource;

use App\Models\PostHistory;
use App\Http\Resources\PostHistoryCollection;
use App\Http\Resources\PostHistory as PostHistoryResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    protected $model = Post::class;
    protected $resource = PostResource::class;
    protected $collection = PostCollection::class;

    protected $validations_create = [
        'title' => 'required|max:255',
        'content' => ''
    ];

    public function update(Request $request, $post_id) {
        $post = Post::find($post_id);

        if (is_null($post)) {
            return $this->sendError('Post does not exists.');
        }

        if (!auth()->user()->hasPermission('posts_update') && $post->user_id !== auth()->user()->id) {
            return $this->sendError('Access denied.', []);
        }

        $input = $request->all();

        $validator = Validator::make($input, [
            'title' => 'required|max:255',
            'content' => '',
            'approve' => 'boolean'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', ['errors' => $validator->errors()], 400);
        }

        $history_post = PostHistory::create([
            'post_id' => $post->id,
            'user_id' => $post->user_id,
            'title' => $post->title,
            'content' => $post->content
        ]);

        $post->title = $input['title'];
        $post->content = $input['content'];

        if ($input['approve'] && auth()->user()->hasPermission('posts_approve')) {
            $post->approved_by = auth()->user()->id;
            $post->approved_at = now();
        }

        $post->save();

        return $this->sendResponse([
            'post' => new PostResource($post),
            'history_post' => new PostResource($history_post)
        ], 'Post updated successfully.');
    }
}
