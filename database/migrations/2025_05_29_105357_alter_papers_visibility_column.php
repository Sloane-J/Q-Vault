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
        // First, change the column to a string type.
        // The default value 'public' must be set here to avoid errors.
        Schema::table('papers', function (Blueprint $table) {
            $table->string('is_visible')->default('public')->change();
        });

        // Now, add the check constraint to enforce the enum-like behavior.
        DB::statement("ALTER TABLE papers ADD CONSTRAINT papers_is_visible_check CHECK (is_visible IN ('public', 'restricted'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First, drop the check constraint.
        DB::statement('ALTER TABLE papers DROP CONSTRAINT papers_is_visible_check');

        // Then, revert the column back to a boolean type,
        // which matches its original state.
        Schema::table('papers', function (Blueprint $table) {
            $table->boolean('is_visible')->default(true)->change();
        });
    }
};
