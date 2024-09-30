<?php

namespace App\Services;
use App\Models\Category;
use Twilio\Rest\Client;
use Illuminate\Http\Response;
use App\Http\Responses\ApiSuccessResponse;
use App\Http\Responses\ApiErrorResponse;

class CategoryService
{
    public function index()
    {
        $categories = Category::where('exist', true)->get();

        if ($categories->isEmpty()) {
            return new ApiErrorResponse('no categories found!',Response::HTTP_NOT_FOUND);
        }

        return new ApiSuccessResponse('this is the all Categories',$categories);
    }



    public function createCategory($data)
    {
        $imageUrl = $data['image'];
        $newName = time() . '.' . $imageUrl->getClientOriginalExtension();
        $imageUrl->move(public_path('category_images'), $newName);
        $imageUrl = url('category_images/' . $newName);

        return Category::create([
            'category' => $data['category'],
            'image' => $imageUrl,
        ]);
    }


    public function editCategory($data,$id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(
                ['message' => 'Category not found'],
                404
            );
        }


            $category->category = $data['category'];

        if (isset($data['image'])) {
            $imageUrl = $data['image'];
            $newName = time() . '.' . $imageUrl->getClientOriginalExtension();
            $imageUrl->move(public_path('category_images'), $newName);
            if ($category->image && file_exists(public_path('category_images/' . basename($category->image)))) {
                unlink(public_path('category_images/' . basename($category->image)));
            }
            $category->image = url('category_images/' . $newName);
        }
        $category->save();
        return $category;
    }

}
