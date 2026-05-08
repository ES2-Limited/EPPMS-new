<?php

use App\Filament\Pages\AddProjectPersonnel;
use App\Filament\Pages\MilestoneTasks;
use App\Filament\Pages\ProjectMilestones;
use App\Filament\Pages\ProjectPersonnels;
use App\Models\Contractor;
use App\Models\Department;
use App\Models\Directorate;
use App\Models\Office;
use App\Models\Personnel;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function (): void {
    Route::get(
        '/admin/project/milestones/{project}',
        ProjectMilestones::class
    )->name('filament.admin.pages.project-milestones');

    Route::get(
        '/admin/project/personnels/{project}',
        ProjectPersonnels::class
    )->name('filament.admin.pages.project-personnels');

    Route::get(
        '/admin/project/personnels/{project}/create',
        AddProjectPersonnel::class
    )->name('filament.admin.pages.add-project-personnel');

    Route::get(
        '/admin/project/milestone/{milestone}/tasks',
        MilestoneTasks::class
    )->name('filament.admin.pages.milestone-tasks');

    Route::get('/admin/offices/records/print', function () {
        return view('prints.offices', [
            'offices' => Office::query()->orderBy('name')->get(),
        ]);
    })->name('admin.offices.records.print');

    Route::get('/admin/directorates/records/print', function () {
        return view('prints.directorates', [
            'directorates' => Directorate::query()->orderBy('name')->get(),
        ]);
    })->name('admin.directorates.records.print');

    Route::get('/admin/departments/records/print', function () {
        return view('prints.departments', [
            'departments' => Department::query()->with('directorate')->orderBy('name')->get(),
        ]);
    })->name('admin.departments.records.print');

    Route::get('/admin/personnels/records/print', function () {
        return view('prints.personnels', [
            'personnel' => Personnel::query()->with(['user.roles', 'directorate', 'department', 'office'])->latest()->get(),
        ]);
    })->name('admin.personnels.records.print');

    Route::get('/admin/firms/records/print', function () {
        return view('prints.firms', [
            'firms' => Contractor::query()->with('user')->orderBy('firm_type_id')->get(),
        ]);
    })->name('admin.firms.records.print');
});
