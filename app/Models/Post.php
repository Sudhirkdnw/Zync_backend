<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    public $table = "posts";

    protected $fillable = [
        'user_id',
        'desc',
        'tags',
        'link_preview_json',
        'interest_ids',
    ];
    
    public function content()
    {
        return $this->hasMany(PostContent::class, 'post_id', 'id');
    }
    
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public static function processPosts($posts, $userId)
    {
        $postIds = $posts->pluck('id');
        $blockedUserIds = User::where('is_block', 1)->pluck('id');

        $likedPosts = Like::whereIn('post_id', $postIds)
                        ->where('user_id', $userId)
                        ->pluck('post_id')
                        ->toArray();

        $commentCounts = Comment::whereIn('post_id', $postIds)
                                ->whereNotIn('user_id', $blockedUserIds)
                                ->selectRaw('post_id, COUNT(*) as count')
                                ->groupBy('post_id')
                                ->pluck('count', 'post_id');

        $likeCounts = Like::whereIn('post_id', $postIds)
                        ->whereNotIn('user_id', $blockedUserIds)
                        ->selectRaw('post_id, COUNT(*) as count')
                        ->groupBy('post_id')
                        ->pluck('count', 'post_id');

        foreach ($posts as $post) {
            $post->is_like = in_array($post->id, $likedPosts) ? 1 : 0;
            $post->comments_count = $commentCounts[$post->id] ?? 0;
            $post->likes_count = $likeCounts[$post->id] ?? 0;
        }

        return $posts;
    }

}
