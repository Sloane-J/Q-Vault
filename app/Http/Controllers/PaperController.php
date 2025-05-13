<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Download;
use App\Models\Level;
use App\Models\Paper;
use App\Models\PaperVersion;
use App\Models\StudentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Barryvdh\Snappy\Facades\SnappyPdf;

class PaperController extends Controller
{
    /**
     * Display a listing of papers.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // This method can be used for non-Livewire paper listings
        $papers = Paper::with(['department', 'studentType', 'level', 'user'])
            ->where('visibility', true)
            ->latest()
            ->paginate(10);
        
        return view('papers.index', compact('papers'));
    }

    /**
     * Show the form for creating a new paper.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // If we need a non-Livewire upload form
        $departments = Department::all();
        $studentTypes = StudentType::all();
        $levels = Level::all();
        
        return view('papers.create', compact('departments', 'studentTypes', 'levels'));
    }

    /**
     * Store a newly created paper in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'paper_file' => 'required|file|mimes:pdf,doc,docx|max:10240',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'department_id' => 'required|exists:departments,id',
            'semester' => 'required|integer|between:1,3',
            'exam_type' => 'required|string|in:midterm,final,quiz,assignment',
            'course_name' => 'required|string|max:255',
            'exam_year' => 'required|integer|min:2000|max:' . (date('Y') + 1),
            'student_type_id' => 'required|exists:student_types,id',
            'level_id' => 'required|exists:levels,id',
            'visibility' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Proceed with file handling
        $file = $request->file('paper_file');
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('papers', $filename, 'public');

        // Create the paper record
        $paper = Paper::create([
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $path,
            'department_id' => $request->department_id,
            'semester' => $request->semester,
            'exam_type' => $request->exam_type,
            'course_name' => $request->course_name,
            'exam_year' => $request->exam_year,
            'student_type_id' => $request->student_type_id,
            'level_id' => $request->level_id,
            'visibility' => $request->has('visibility') ? true : false,
            'user_id' => Auth::id(),
        ]);

        // Create initial version
        PaperVersion::create([
            'paper_id' => $paper->id,
            'version_number' => 1,
            'file_path' => $path,
            'notes' => 'Initial upload',
        ]);

        return redirect()->route('papers.show', $paper->id)->with('success', 'Paper uploaded successfully!');
    }

    /**
     * Display the specified paper.
     *
     * @param  \App\Models\Paper  $paper
     * @return \Illuminate\Http\Response
     */
    public function show(Paper $paper)
    {
        // Check visibility
        if (!$paper->visibility && Auth::id() !== $paper->user_id && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access');
        }
        
        // Load versions and other relationships
        $paper->load(['versions', 'department', 'studentType', 'level', 'user']);
        
        return view('papers.show', compact('paper'));
    }

    /**
     * Show the form for editing the specified paper.
     *
     * @param  \App\Models\Paper  $paper
     * @return \Illuminate\Http\Response
     */
    public function edit(Paper $paper)
    {
        // Make sure only admins or the owner can edit
        if (Auth::id() !== $paper->user_id && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action');
        }
        
        $departments = Department::all();
        $studentTypes = StudentType::all();
        $levels = Level::all();
        
        return view('papers.edit', compact('paper', 'departments', 'studentTypes', 'levels'));
    }

    /**
     * Update the specified paper in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Paper  $paper
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Paper $paper)
    {
        // Make sure only admins or the owner can update
        if (Auth::id() !== $paper->user_id && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action');
        }
        
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'department_id' => 'required|exists:departments,id',
            'semester' => 'required|integer|between:1,3',
            'exam_type' => 'required|string|in:midterm,final,quiz,assignment',
            'course_name' => 'required|string|max:255',
            'exam_year' => 'required|integer|min:2000|max:' . (date('Y') + 1),
            'student_type_id' => 'required|exists:student_types,id',
            'level_id' => 'required|exists:levels,id',
            'visibility' => 'boolean',
            'paper_file' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Update the paper metadata
        $paper->title = $request->title;
        $paper->description = $request->description;
        $paper->department_id = $request->department_id;
        $paper->semester = $request->semester;
        $paper->exam_type = $request->exam_type;
        $paper->course_name = $request->course_name;
        $paper->exam_year = $request->exam_year;
        $paper->student_type_id = $request->student_type_id;
        $paper->level_id = $request->level_id;
        $paper->visibility = $request->has('visibility') ? true : false;

        // Check if a new file was uploaded
        if ($request->hasFile('paper_file')) {
            $file = $request->file('paper_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('papers', $filename, 'public');
            
            // Get the latest version number
            $latestVersion = $paper->versions()->max('version_number');
            
            // Create a new version
            PaperVersion::create([
                'paper_id' => $paper->id,
                'version_number' => $latestVersion + 1,
                'file_path' => $path,
                'notes' => $request->version_notes ?? 'Updated version',
            ]);
            
            // Update the paper's file path to the newest version
            $paper->file_path = $path;
        }
        
        $paper->save();
        
        return redirect()->route('papers.show', $paper->id)->with('success', 'Paper updated successfully!');
    }

    /**
     * Remove the specified paper from storage.
     *
     * @param  \App\Models\Paper  $paper
     * @return \Illuminate\Http\Response
     */
    public function destroy(Paper $paper)
    {
        // Make sure only admins or the owner can delete
        if (Auth::id() !== $paper->user_id && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action');
        }
        
        // Get all file paths associated with this paper
        $filePaths = [$paper->file_path];
        $versions = $paper->versions()->get();
        foreach ($versions as $version) {
            $filePaths[] = $version->file_path;
        }
        
        // Delete all files
        foreach (array_unique($filePaths) as $path) {
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
        
        // Delete versions and paper
        $paper->versions()->delete();
        $paper->delete();
        
        return redirect()->route('papers.index')->with('success', 'Paper deleted successfully!');
    }

    /**
     * Download a specific paper and track the download.
     *
     * @param  \App\Models\Paper  $paper
     * @return \Illuminate\Http\Response
     */
    public function download(Paper $paper)
    {
        // Check visibility
        if (!$paper->visibility && Auth::id() !== $paper->user_id && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access');
        }
        
        // Track the download
        Download::create([
            'user_id' => Auth::id(),
            'paper_id' => $paper->id,
            'downloaded_at' => now(),
        ]);
        
        // Get the file path
        $path = Storage::disk('public')->path($paper->file_path);
        
        // Get filename for the download
        $originalName = pathinfo($paper->file_path, PATHINFO_FILENAME);
        $extension = pathinfo($paper->file_path, PATHINFO_EXTENSION);
        $downloadName = $paper->title . '_' . $paper->exam_year . '.' . $extension;
        
        // Return the file download
        return response()->download($path, $downloadName);
    }

    /**
     * Download a specific version of a paper.
     *
     * @param  \App\Models\PaperVersion  $version
     * @return \Illuminate\Http\Response
     */
    public function downloadVersion(PaperVersion $version)
    {
        $paper = $version->paper;
        
        // Check visibility
        if (!$paper->visibility && Auth::id() !== $paper->user_id && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access');
        }
        
        // Track the download (for the main paper)
        Download::create([
            'user_id' => Auth::id(),
            'paper_id' => $paper->id,
            'downloaded_at' => now(),
        ]);
        
        // Get the file path
        $path = Storage::disk('public')->path($version->file_path);
        
        // Get filename for the download
        $originalName = pathinfo($version->file_path, PATHINFO_FILENAME);
        $extension = pathinfo($version->file_path, PATHINFO_EXTENSION);
        $downloadName = $paper->title . '_v' . $version->version_number . '.' . $extension;
        
        // Return the file download
        return response()->download($path, $downloadName);
    }

    /**
     * Preview a paper in the browser.
     *
     * @param  \App\Models\Paper  $paper
     * @return \Illuminate\Http\Response
     */
    public function preview(Paper $paper)
    {
        // Check visibility
        if (!$paper->visibility && Auth::id() !== $paper->user_id && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access');
        }
        
        // Get the file path
        $path = Storage::disk('public')->path($paper->file_path);
        
        // For PDFs, we can display directly
        if (pathinfo($path, PATHINFO_EXTENSION) === 'pdf') {
            return response()->file($path);
        }
        
        // For other formats, we need to convert to PDF first using Snappy
        $pdf = SnappyPdf::loadFile($path);
        return $pdf->inline($paper->title . '.pdf');
    }

    /**
     * Get departments, student types, and levels for form selects.
     * (Can be used as an API endpoint for Livewire components)
     *
     * @return \Illuminate\Http\Response
     */
    public function getFormData()
    {
        $departments = Department::all();
        $studentTypes = StudentType::all();
        $levels = Level::all();
        
        return response()->json([
            'departments' => $departments,
            'studentTypes' => $studentTypes,
            'levels' => $levels,
        ]);
    }

    /**
     * Get levels for a specific student type.
     * (Used for dynamic form selects)
     *
     * @param  int  $studentTypeId
     * @return \Illuminate\Http\Response
     */
    public function getLevelsByStudentType($studentTypeId)
    {
        $levels = Level::where('student_type_id', $studentTypeId)->get();
        return response()->json($levels);
    }
}