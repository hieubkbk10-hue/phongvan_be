<?php

namespace App\Containers\AppSection\Media\Tests\Unit;

use App\Containers\AppSection\Media\Tests\TestCase;
use Illuminate\Support\Facades\Schema;

/**
 * Class MediaMigrationTest.
 *
 * @group media
 * @group unit
 */
class MediaMigrationTest extends TestCase
{
    public function test_media_table_has_expected_columns(): void
    {
        $columns = [
            'id',
            // add your migration columns
            'created_at',
            'updated_at',
        ];

        foreach ($columns as $column) {
            $this->assertTrue(Schema::hasColumn('media', $column));
        }
    }
}
