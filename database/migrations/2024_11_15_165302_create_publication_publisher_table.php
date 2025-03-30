<?php

use App\Models\Publication;
use App\Models\Publisher;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('publication_publisher', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Publication::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Publisher::class)->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['publication_id', 'publisher_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('publication_publisher');
    }
};
