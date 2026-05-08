<x-filament-panels::page>
    @php
        $project = $this->project;
        $canManage = auth()->user()?->hasAnyRole(['admin', 'organization_admin']) ?? false;
        $statusColors = [
            'pending' => '#e5e7eb',
            'in_progress' => '#BFDBFE',
            'done' => '#FEF08A',
            'completed' => '#BBF7D0',
        ];
    @endphp

    <div style="display:grid; gap:20px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0; gap:16px; flex-wrap:wrap;">
            <h2 style="font-size:18px; font-weight:700; color:#111827; margin:0; flex:1;">{{ $project->name }} Project Milestones</h2>

            <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
                <button type="button" onclick="window.print()" style="background:#06B6D4; color:white; border:none; padding:8px 16px; border-radius:6px; cursor:pointer; font-size:13px; font-weight:500; display:flex; align-items:center; gap:6px;">
                    Print Report
                </button>

                <a href="{{ \App\Filament\Resources\ProjectResource::getUrl('view', ['record' => $project]) }}" style="background:#0D1B3E; color:white; padding:8px 16px; border-radius:6px; font-size:13px; font-weight:500; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                    &larr; Back to Project
                </a>

                @if ($canManage)
                    <a href="{{ \App\Filament\Resources\MilestoneResource::getUrl('create').'?project_ulid='.$project->ulid }}" style="background:#16A34A; color:white; padding:8px 16px; border-radius:6px; font-size:13px; font-weight:500; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                        + Add Project Milestone
                    </a>
                @endif
            </div>
        </div>

        <div style="overflow:hidden; border:1px solid #e5e7eb; border-radius:12px; background:#ffffff; box-shadow:0 1px 3px rgba(0,0,0,0.08);">
            <table style="width:100%; border-collapse:collapse; font-size:14px;">
                <thead style="background:#f9fafb; color:#374151; font-weight:600; text-align:left;">
                    <tr>
                        <th style="padding:12px 16px; border-bottom:1px solid #e5e7eb;">Name</th>
                        <th style="padding:12px 16px; border-bottom:1px solid #e5e7eb;">Amount</th>
                        <th style="padding:12px 16px; border-bottom:1px solid #e5e7eb;">Progress</th>
                        <th style="padding:12px 16px; border-bottom:1px solid #e5e7eb;">Status</th>
                        <th style="padding:12px 16px; border-bottom:1px solid #e5e7eb;">Tasks</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->getMilestones() as $milestone)
                        @php
                            $tasks = $milestone->tasks->whereNull('deleted_at');
                            $totalTasks = $tasks->count();
                            $doneTasks = $tasks->where('status', 'done')->count();
                            $progress = $totalTasks > 0 ? round(($doneTasks / $totalTasks) * 100, 2) : 0;
                            $status = $progress >= 100 ? 'done' : ($progress > 0 ? 'in_progress' : 'pending');
                            $statusBg = $statusColors[$status] ?? '#e5e7eb';
                        @endphp
                        <tr>
                            <td style="padding:12px 16px; border-bottom:1px solid #f3f4f6; font-weight:600; color:#111827;">{{ $milestone->name }}</td>
                            <td style="padding:12px 16px; border-bottom:1px solid #f3f4f6; color:#111827;">{{ number_format((float) $milestone->amount, 2) }}</td>
                            <td style="padding:12px 16px; border-bottom:1px solid #f3f4f6; color:#111827;">{{ number_format($progress, 2) }}%</td>
                            <td style="padding:12px 16px; border-bottom:1px solid #f3f4f6; color:#111827;">
                                <span style="background:{{ $statusBg }}; padding:4px 10px; border-radius:12px; font-size:12px; color:#111827;">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                                <a href="{{ \App\Filament\Resources\MilestoneResource::getUrl('report', ['record' => $milestone]) }}" style="background:#06B6D4; color:white; padding:3px 8px; border-radius:4px; font-size:11px; text-decoration:none; margin-left:4px;">Print</a>
                            </td>
                            <td style="padding:12px 16px; border-bottom:1px solid #f3f4f6; color:#111827;">
                                {{ $totalTasks }}
                                <a href="{{ route('filament.admin.pages.milestone-tasks', ['milestone' => $milestone->ulid]) }}" style="background:#2563EB; color:white; padding:3px 8px; border-radius:4px; font-size:11px; text-decoration:none; margin-left:4px;">View Tasks</a>
                                @if ($canManage)
                                    <a href="{{ \App\Filament\Resources\TaskResource::getUrl('create').'?milestone_id='.$milestone->id }}" style="background:#EA580C; color:white; padding:3px 8px; border-radius:4px; font-size:11px; text-decoration:none; margin-left:4px;">Add Task</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="padding:32px 16px; text-align:center; color:#6b7280;">No milestones added to this project yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>
