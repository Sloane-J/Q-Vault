<?php

use Illuminate\Support\Facades\Validator;

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'paper_file' => 'required|file|mimes:pdf,doc,docx|max:10240',
            'title' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Proceed with file handling
        $file = $request->file('paper_file');
        $path = $file->store('papers', 'public');

        Paper::create([
            'title' => $request->title,
            'file_path' => $path,
        ]);

        return redirect()->back()->with('success', 'Paper uploaded!');
    }