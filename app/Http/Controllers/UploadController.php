<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;                                                                          

class UploadController extends Controller
{

    public function uploadImage(Request $request) {
        try {
            $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $path = $request->file('image')->store('images', 'public');

        $url = Storage::disk('public')->url($path);

        return response()->json([
            back()->with('success', 'image uploaded!')->with('image_url', $url),
        ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function uploadFile(Request $request) {
        try {
            $request->validate([
                'document' => 'required|mimes:pdf,doc,docx,txt,md',
            ]);

            $file = $request->file('document');

            $originalName = $file->getClientOriginalName();
            $originalExt = $file->getClientOriginalExtension();

            $path = $file->store('document');

            return response()->json([
                'original_name' => $originalName,
                'original_extension' => $originalExt,
                'stored_path' => $path,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function putFile() {
        try {
            $put = Storage::disk('local')->put('document/reports/secret.txt', 'This is plain text secret file.', 'private');

            return response()->json([
                'your file is stored.',
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function customDisk() {
        try {
            $put = Storage::disk('custom')->put('document/example.txt', 'This is plain text file');

            return response()->json([
                'Your file stored successfully.',
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function readFile() {
        try {
            $path = 'document/command.txt';

            if (!Storage::disk('local')->exists($path)) {
                abort(404, 'File not found');
            }

            return Storage::disk('local')->get($path);

        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function deleteFile() {
        try {
            $path = 'document/command.txt';

            if (!Storage::disk('local')->exists($path)) {
                abort(404, 'File not found!');
            }

            return response()->json([
                Storage::disk('local')->delete($path),
                'message' => 'file deleted successfully.',
            ], 200);

        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function downloadFile() {
        try {
            $path = 'images/firstImage.png';

            if (!Storage::disk('public')->exists($path)) {
                abort(404, 'File not found.');
            }

            return response()->json([
                Storage::disk('public')->download('images', $path),
                'message' => 'File Downloaded successfully',
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function getFileSize() {
        try {
            $path = 'document/command.txt';

            if (!Storage::disk('local')->exists($path)) {
                abort(404, 'File not found');
            }

            $file = Storage::size($path);

            return response()->json([
                'your file size is ' . $file->getSize(),
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function getVisibility() {
        try {
            $visibility = Storage::disk('public')->getVisibility('private/secret.txt');

            return response()->json([
                'Your file visibility is ' => $visibility,
            ], 200);
         } catch (\Exception $e) {
             report($e);

             return response()->json([
                'message' => 'Something went wrong.'
             ], 500);
         } 
    }

    public function createDirectory() {
        try {
            $make = Storage::disk('local')->makeDirectory('document/reports');

            return response()->json([
                'Directory created successfully.',
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }


    public function moveOrRename() {
        try {
            $move = Storage::disk('local')->move('document/reports', 'document/old_reports');

            return response()->json([
                'Your directory name changed successfully',
            ], 200);

        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

}

