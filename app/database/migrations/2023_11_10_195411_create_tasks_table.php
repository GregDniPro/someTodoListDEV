<?php

declare(strict_types=1);

use App\Enums\Status;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->mediumText('description');
            $table->enum('status', [Status::TODO->value, Status::DONE->value])
                ->default(Status::TODO->value);
            $table->tinyInteger('priority')->nullable(false);
            $table->timestamp('completed_at')->default(null);
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->timestamps();

            //TODO indexes for get tasks user+filters?
            $table->foreign('parent_id')
                ->references('id')
                ->on('tasks')
                ->onDelete('cascade');
            $table->fullText(['title', 'description']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
        });
        Schema::dropIfExists('tasks');
    }
};
