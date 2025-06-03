<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, let's handle any existing data by creating a temporary mapping
        $this->migrateExistingData();
        
        Schema::table('papers', function (Blueprint $table) {
            // Add the new level_id foreign key column
            $table->foreignId('level_id')->nullable()->after('semester')->constrained('levels')->onDelete('cascade');
        });
        
        // Now populate the level_id for existing records
        $this->populateLevelIds();
        
        Schema::table('papers', function (Blueprint $table) {
            // Make level_id required now that it's populated
            $table->foreignId('level_id')->nullable(false)->change();
            
            // Drop the old columns
            $table->dropColumn(['level', 'student_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('papers', function (Blueprint $table) {
            // Add back the old columns
            $table->string('student_type')->after('semester');
            $table->integer('level')->after('student_type');
        });
        
        // Restore the old data format
        $this->restoreOldData();
        
        Schema::table('papers', function (Blueprint $table) {
            // Drop the foreign key and level_id column
            $table->dropForeign(['level_id']);
            $table->dropColumn('level_id');
        });
    }
    
    /**
     * Handle existing data before migration
     */
    private function migrateExistingData(): void
    {
        // Check if there are any existing papers
        $existingPapers = DB::table('papers')->exists();
        
        if (!$existingPapers) {
            // No existing data, nothing to migrate
            return;
        }
        
        // Log the migration for reference
        \Log::info('Migrating existing papers data to use level_id relationships');
    }
    
    /**
     * Populate level_id based on existing level and student_type data
     */
    private function populateLevelIds(): void
    {
        // Get all papers that need level_id populated
        $papers = DB::table('papers')
            ->whereNull('level_id')
            ->get(['id', 'level', 'student_type']);
            
        foreach ($papers as $paper) {
            // Find the corresponding level_id
            $levelId = DB::table('levels')
                ->join('student_type', 'levels.student_type_id', '=', 'student_type.id')
                ->where('levels.level_number', $paper->level)
                ->where('student_type.name', $paper->student_type)
                ->value('levels.id');
                
            if ($levelId) {
                // Update the paper with the correct level_id
                DB::table('papers')
                    ->where('id', $paper->id)
                    ->update(['level_id' => $levelId]);
            } else {
                // Log missing level mapping
                \Log::warning("Could not find level mapping for paper ID {$paper->id}: Level {$paper->level}, Student Type {$paper->student_type}");
            }
        }
    }
    
    /**
     * Restore old data format when rolling back
     */
    private function restoreOldData(): void
    {
        // Get all papers with level_id
        $papers = DB::table('papers')
            ->join('levels', 'papers.level_id', '=', 'levels.id')
            ->join('student_type', 'levels.student_type_id', '=', 'student_type.id')
            ->get([
                'papers.id', 
                'levels.level_number as level', 
                'student_type.name as student_type'
            ]);
            
        foreach ($papers as $paper) {
            DB::table('papers')
                ->where('id', $paper->id)
                ->update([
                    'level' => $paper->level,
                    'student_type' => $paper->student_type
                ]);
        }
    }
};