<?php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'is_published',
        'published_at',
        'author_id',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    // Accessor example
    public function getSummaryAttribute(): string
    {
        return Str::limit($this->content, 100);
    }

    // Scope example
    public function scopePublished($query)
    {
        return $query->where('is_published', true)
            ->where('published_at', '<=', Carbon::now());
    }

    // Relation example
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    // Custom method to publish the post
    public function publish()
    {
        $this->update([
            'is_published' => true,
            'published_at' => Carbon::now(),
        ]);

        logger()->info('Post published successfully!', [
            'post_id' => $this->id,
            'title'   => $this->title,
        ]);

        // Localized
        return __('The post has been published!');
    }

    // Custom method to unpublish the post
    public function unpublish()
    {
        $this->update([
            'is_published' => false,
            'published_at' => null,
        ]);

        // Unlocalized raw string
        logger()->warning('Post unpublished.', [
            'post_id' => $this->id,
            'title'   => $this->title,
        ]);

        return 'The post is now unpublished.'; // intentionally unlocalized
    }

    // Notification with both localized and unlocalized parts
    public function notifyAuthor()
    {
        $message = __('Hello :name, your post ":title" was viewed :count times!', [
            'name'  => $this->author->name ?? 'Author',
            'title' => $this->title,
            'count' => rand(1, 100),
        ]);

        $extra = 'Check your dashboard for more stats.'; // unlocalized

        logger()->info($message . ' ' . $extra);

        return $message . ' ' . $extra;
    }

    // Greeting visitor
    public function greetVisitor(string $visitorName): string
    {
        return 'Welcome, ' . $visitorName . '! Enjoy reading "' . $this->title . '".'; // unlocalized
    }

    // Example method with mixed messages
    public function displayStatus()
    {
        if ($this->is_published) {
            return __('This post is live!'); // localized
        }

        return 'This post is currently in draft.'; // unlocalized
    }

    // Example with embedded human text
    public function commentsSection()
    {
        $prompt = __('Leave your thoughts below:');              // localized
        $tip    = 'Remember to be respectful to other readers.'; // unlocalized

        return $prompt . ' ' . $tip;
    }
}
