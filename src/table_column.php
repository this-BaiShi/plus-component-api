<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

$component_table_name = 'component_example';

if (!Schema::hasColumn($component_table_name, 'data')) {
    Schema::table($component_table_name, function (Blueprint $table) {
        $table->string('data')->nullable();
    });
}

