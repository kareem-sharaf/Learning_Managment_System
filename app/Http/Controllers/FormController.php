<?php

namespace App\Http\Controllers;

use App\Models\Form;
use Illuminate\Http\Request;

class FormController extends Controller
{
/*********************************** */
    public function index()
    {
     $forms=Form::get();
     $message = "this is the all forms";

        return response()->json([
            'message' => $message,
            'data' => $forms
        ]);
    }

    /*********************************************** */
    public function create(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'FormName' => 'required',
             ]);
        $form = Form::create([
            'FormName' => $request->FormName,
            ]);
            $message = "form added successfully.";
            return response()->json([
                'message' => $message,
                'data' => $form
            ]);
    }
/*************************************************** */
    public function edit(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'form_id' => 'required',
            'FormName' => 'required',
            ]);

        $form = Form::find($request->form_id);
        if (!$form) {
            return response()->json([
                'message' => 'form not found.'
            ]);
        }
        $form->update([
            'FormName' => $request->FormName
        ]);
        $message = "The form edit successfully.";
        return response()->json([
            'message' => $message,
            'data' => $form
        ]);
    }

   /************************************************ */
    public function destroy($form_id)
    {
        $user = auth()->user();
        $form = Form::find($form_id);
        if (!$form) {
            $message = "The form doesn't exist.";
            return response()->json([
                'message' => $message,
            ]);
        }

        $form->delete();

        $message = "The form deleted successfully.";
        return response()->json([
            'message' => $message,
        ]);
    }
    /************************************************** */
}
