<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * - Each note now belongs to a user (user_id), so users only see their own notes.
     * - content becomes LONGTEXT so long rich-text notes are not cut at 255 characters.
     *
     * Existing notes get their owner from the old `name` column when it matches a user's
     * name. Notes that cannot be matched keep user_id = NULL and are hidden until assigned,
     * e.g. UPDATE keep_notes SET user_id = 1 WHERE user_id IS NULL;
     */
    public function up()
    {
        Schema::table('keep_notes', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE keep_notes MODIFY content LONGTEXT NOT NULL');

            // Older databases have a manually added `name` column that the frontend used to
            // filter notes by user. Keep it, but make it optional, and use it once to give
            // existing notes their owner.
            if (Schema::hasColumn('keep_notes', 'name')) {
                DB::statement('ALTER TABLE keep_notes MODIFY name VARCHAR(255) NULL');
                DB::statement('UPDATE keep_notes n JOIN users u ON u.name = n.name SET n.user_id = u.id WHERE n.user_id IS NULL');
            }
        }
    }

    public function down()
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE keep_notes MODIFY content VARCHAR(255) NOT NULL');
        }

        Schema::table('keep_notes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
