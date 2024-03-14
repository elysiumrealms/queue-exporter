<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $schema = Schema::connection($this->getConnection());

        $schema->create('queue_exports', function (Blueprint $table) {
            $table->uuid('id')
                ->primary();
            $table->string('filename')
                ->unique();
            $table->smallInteger('status')
                ->default(0)
                ->comment('0: Pending, 1: Processing, 2: Completed');
            $table->dateTime('expires_at')
                ->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $schema = Schema::connection($this->getConnection());

        $schema->dropIfExists('queue_exports');
    }

    /**
     * Get the migration connection name.
     *
     * @return string|null
     */
    public function getConnection()
    {
        return config('queue-exporter.storage.database.connection');
    }
};
