<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offices', function (Blueprint $table) {
            if (! Schema::hasColumn('offices', 'type')) {
                $table->string('type')->nullable()->after('name');
            }

            if (! Schema::hasColumn('offices', 'state')) {
                $table->string('state')->nullable()->after('address');
            }

            if (! Schema::hasColumn('offices', 'lga')) {
                $table->string('lga')->nullable()->after('state');
            }

            if (! Schema::hasColumn('offices', 'email')) {
                $table->string('email')->nullable()->after('lga');
            }

            if (! Schema::hasColumn('offices', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }
        });

        Schema::table('directorates', function (Blueprint $table) {
            if (! Schema::hasColumn('directorates', 'directorate_code')) {
                $table->string('directorate_code')->nullable()->after('ulid');
            }
        });

        Schema::table('departments', function (Blueprint $table) {
            if (! Schema::hasColumn('departments', 'department_code')) {
                $table->string('department_code')->nullable()->after('ulid');
            }

            if (! Schema::hasColumn('departments', 'function')) {
                $table->text('function')->nullable()->after('name');
            }
        });

        Schema::table('units', function (Blueprint $table) {
            if (! Schema::hasColumn('units', 'function')) {
                $table->text('function')->nullable()->after('name');
            }
        });

        Schema::table('personnel', function (Blueprint $table) {
            if (! Schema::hasColumn('personnel', 'first_name')) {
                $table->string('first_name')->nullable()->after('user_id');
            }

            if (! Schema::hasColumn('personnel', 'last_name')) {
                $table->string('last_name')->nullable()->after('first_name');
            }

            if (! Schema::hasColumn('personnel', 'other_name')) {
                $table->string('other_name')->nullable()->after('last_name');
            }

            if (! Schema::hasColumn('personnel', 'phone')) {
                $table->string('phone')->nullable()->after('other_name');
            }

            if (! Schema::hasColumn('personnel', 'designation')) {
                $table->string('designation')->nullable()->after('phone');
            }
        });

        Schema::table('contractors', function (Blueprint $table) {
            if (! Schema::hasColumn('contractors', 'phone')) {
                $table->string('phone')->nullable()->after('firm_type_id');
            }

            if (! Schema::hasColumn('contractors', 'website')) {
                $table->string('website')->nullable()->after('phone');
            }

            if (! Schema::hasColumn('contractors', 'logo')) {
                $table->string('logo')->nullable()->after('website');
            }
        });

        Schema::table('contractor_personnel', function (Blueprint $table) {
            if (! Schema::hasColumn('contractor_personnel', 'first_name')) {
                $table->string('first_name')->nullable()->after('contractor_id');
            }

            if (! Schema::hasColumn('contractor_personnel', 'last_name')) {
                $table->string('last_name')->nullable()->after('first_name');
            }

            if (! Schema::hasColumn('contractor_personnel', 'other_name')) {
                $table->string('other_name')->nullable()->after('last_name');
            }

            if (! Schema::hasColumn('contractor_personnel', 'designation')) {
                $table->string('designation')->nullable()->after('phone');
            }
        });
    }

    public function down(): void
    {
        // Intentionally non-destructive: this migration only adds nullable fields for UAT forms.
    }
};
