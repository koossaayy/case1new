<?php
namespace App\Http\Controllers;

use App\Models\Post;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PostController extends Controller
{
    // Show a single post
    public function show(Post $post)
    {
        // Localized title for the page
        $pageTitle = __('Reading Post: :title', ['title' => $post->title]);

        // Unlocalized subtitle
        $pageSubtitle = 'Dive into the latest updates from our authors.';

        return view('posts.show', compact('post', 'pageTitle', 'pageSubtitle'));
    }

    // Publish a post
    public function publish(Post $post)
    {
        $message = $post->publish(); // already localized inside model

        // Extra unlocalized log
        Log::info('Admin triggered publish action.');

        return redirect()->back()->with('status', $message);
    }

    // Unpublish a post
    public function unpublish(Post $post)
    {
        $message = $post->unpublish(); // unlocalized string inside model

        // Localized notification
        $adminMessage = __('The post has been unpublished successfully.');

        return redirect()->back()->with('status', $adminMessage . ' ' . $message);
    }

    // List all published posts
    public function index()
    {
        $posts = Post::published()->get();

        // Localized welcome message
        $welcome = __('Welcome to our blog!');

        // Unlocalized tip
        $tip = 'Scroll down to find more interesting articles.';

        return view('posts.index', compact('posts', 'welcome', 'tip'));
    }

    // Example: send greeting to visitor
    public function greetVisitor(Request $request, Post $post)
    {
        $visitor = $request->input('name', 'Guest');

                                                   // Uses Post model mixed method
        $greeting = $post->greetVisitor($visitor); // unlocalized inside model

        // Additional localized message
        $footer = __('Thank you for visiting!');

        return response()->json([
            'greeting' => $greeting,
            'footer'   => $footer,
        ]);
    }

    // Method with raw text strings only
    public function rawMessages()
    {
        $msg1 = 'This is an unlocalized message for testing.';
        $msg2 = 'Remember to check all notifications daily.';

        return response()->json([
            'msg1' => $msg1,
            'msg2' => $msg2,
        ]);
    }

    public function validates(string $attribute, mixed $value, Closure $fail): void
    {
        if (! Str::endsWith($value, '.zip')) {
            $fail('The given value must be a path to a zip file.');
        }
    }

}
