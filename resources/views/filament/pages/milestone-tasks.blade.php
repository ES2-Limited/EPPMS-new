<x-filament-panels::page>
    @php
        $milestone = $this->milestone;
        $project = $milestone->project;
        $canManage = auth()->user()?->hasAnyRole(['admin', 'organization_admin']) ?? false;
    @endphp

    <div style="display:grid; gap:20px;">
        <div style="display:flex; justify-content:space-between; align-items:center; gap:16px; flex-wrap:wrap;">
            <h2 style="font-size:18px; font-weight:700; color:#111827; margin:0; flex:1;">{{ $milestone->name }} Tasks</h2>

            <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
                <a href="{{ route('filament.admin.pages.project-milestones', ['project' => $project->ulid]) }}" style="background:#0D1B3E; color:white; padding:8px 16px; border-radius:6px; font-size:13px; font-weight:500; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                    &larr; Back to Milestones
                </a>

                @if ($canManage)
                    <a href="{{ \App\Filament\Resources\TaskResource::getUrl('create').'?milestone_id='.$milestone->id }}" style="background:#EA580C; color:white; padding:8px 16px; border-radius:6px; font-size:13px; font-weight:500; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                        + Add Task
                    </a>
                @endif
            </div>
        </div>

        <div style="overflow:hidden; border:1px solid #e5e7eb; border-radius:12px; background:#ffffff; box-shadow:0 1px 3px rgba(0,0,0,0.08);">
            <table style="width:100%; border-collapse:collapse; font-size:14px;">
                <thead style="background:#f9fafb; color:#374151; font-weight:600; text-align:left;">
                    <tr>
                        <th style="padding:12px 16px; border-bottom:1px solid #e5e7eb;">Name</th>
                        <th style="padding:12px 16px; border-bottom:1px solid #e5e7eb;">Status</th>
                        <th style="padding:12px 16px; border-bottom:1px solid #e5e7eb;">Start Date</th>
                        <th style="padding:12px 16px; border-bottom:1px solid #e5e7eb;">Due Date</th>
                        <th style="padding:12px 16px; border-bottom:1px solid #e5e7eb;">Cost</th>
                        <th style="padding:12px 16px; border-bottom:1px solid #e5e7eb;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->getTasks() as $task)
                        <tr>
                            <td style="padding:12px 16px; border-bottom:1px solid #f3f4f6; font-weight:600; color:#111827;">{{ $task->name }}</td>
                            <td style="padding:12px 16px; border-bottom:1px solid #f3f4f6; color:#111827;">{{ ucfirst($task->status) }}</td>
                            <td style="padding:12px 16px; border-bottom:1px solid #f3f4f6; color:#111827;">{{ $task->start_date?->format('Y-m-d') ?? '-' }}</td>
                            <td style="padding:12px 16px; border-bottom:1px solid #f3f4f6; color:#111827;">{{ $task->due_date?->format('Y-m-d') ?? '-' }}</td>
                            <td style="padding:12px 16px; border-bottom:1px solid #f3f4f6; color:#111827;">{{ number_format((float) $task->cost, 2) }}</td>
                            <td style="padding:12px 16px; border-bottom:1px solid #f3f4f6; color:#111827;">
                                <a href="{{ \App\Filament\Resources\TaskResource::getUrl('view', ['record' => $task]) }}" style="background:#2563EB; color:white; padding:3px 8px; border-radius:4px; font-size:11px; text-decoration:none;">View</a>
                                @if ($canManage)
                                    <a href="{{ \App\Filament\Resources\TaskResource::getUrl('edit', ['record' => $task]) }}" style="background:#5B5FC7; color:white; padding:3px 8px; border-radius:4px; font-size:11px; text-decoration:none; margin-left:4px;">Edit</a>
                                @endif
                                @if ($task->status !== 'done' && auth()->check() && \App\Models\Task::canBeMarkedDoneBy(auth()->user(), $task))
                                    <button type="button" wire:click="markTaskDone({{ $task->id }})" style="background:#16A34A; color:white; padding:3px 8px; border:none; border-radius:4px; font-size:11px; margin-left:4px; cursor:pointer;">Mark Done</button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding:32px 16px; text-align:center; color:#6b7280;">No tasks added to this milestone yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>
