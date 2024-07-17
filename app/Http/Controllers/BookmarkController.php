<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Bookmark;
use App\Models\Video;
use App\Models\Files;
use App\Models\Unit;
use App\Models\Lesson;

class BookmarkController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $bookmarks = $user->bookmarks()->with('bookmarkable')->get();
        if ($bookmarks->isNotEmpty()) {
            return response()->json(['bookmarks' => $bookmarks], 200);
        } else {
            return response()->json(['message' => 'You do not have any bookmarks'], 200);
        }
    }

    public function toggle(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'unit' => 'string|exists:units,name|nullable',
            'lesson' => 'string|exists:lessons,name|nullable',
            'video' => 'string|exists:videos,name|nullable',
            'file' => 'string|exists:files,name|nullable',
        ]);

        $bookmark = null;
        $bookmark_name = '';

        if ($request->has('unit')) {
            $bookmark = Unit::where('name', $request->unit)->first();
            $bookmark_name = $bookmark->name;
        } elseif ($request->has('lesson')) {
            $bookmark = Lesson::where('name', $request->lesson)->first();
            $bookmark_name = $bookmark->name;
        } elseif ($request->has('video')) {
            $bookmark = Video::where('name', $request->video)->first();
            $bookmark_name = $bookmark->name;
        } elseif ($request->has('file')) {
            $bookmark = Files::where('name', $request->file)->first();
            $bookmark_name = $bookmark->name;
        }

        if (!$bookmark) {
            return response()->json(['message' => 'Item not found!'], 404);
        }

        $existingBookmark = Bookmark::where('user_id', $user->id)
            ->where('bookmarkable_id', $bookmark->id)
            ->where('bookmarkable_type', get_class($bookmark))
            ->first();

        if ($existingBookmark) {
            $existingBookmark->delete();
            return response()->json(['message' => 'Bookmark successfully deleted'], 200);
        }

        $newBookmark = new Bookmark([
            'user_id' => $user->id,
            'bookmarkable_id' => $bookmark->id,
            'bookmarkable_type' => get_class($bookmark),
            'bookmark_name' => $bookmark_name,
        ]);

        $newBookmark->save();

        return response()->json([
            'message' => 'Bookmark stored successfully',
            'bookmark' => $newBookmark,
        ], 201);
    }
}
