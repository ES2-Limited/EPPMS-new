<x-filament-panels::page>
@php
    $project = $this->record;

    if (! $project || ! ($project instanceof \App\Models\Project) || ! $project->name || $project->name === \App\Models\Organization::query()->first()?->name) {
        $routeRecord = request()->route('record') ?? request()->route('ulid');
        $project = \App\Models\Project::query()
            ->where('ulid', $routeRecord)
            ->orWhere('id', $routeRecord)
            ->first() ?? $this->record;
    }

    $progress = $project->get_progress();
    $user = auth()->user();
    $canSeeOrgButtons = $user?->hasAnyRole([
        'admin',
        'organization_admin',
        'management_admin',
        'directorate_admin',
        'regional_admin',
        'department_admin',
        'head_of_unit',
        'organization_personnel',
        'auditor',
    ]) ?? false;
    $canManagePersonnel = $user?->hasAnyRole(['admin', 'organization_admin']) ?? false;
    $comments = $project->chats()->with('sender')->orderBy('created_at')->get();
    $recentImages = $project->recentTaskImages(5);
    $milestones = $project->milestones()->whereNull('deleted_at')->with('tasks')->get();
    $completed = $milestones->filter(function ($milestone) {
        $tasks = $milestone->tasks->whereNull('deleted_at');

        return $tasks->count() > 0 && $tasks->where('status', 'done')->count() === $tasks->count();
    })->count();
    $remaining = max(0, $milestones->count() - $completed);
    $personnelCount = $project->projectPersonnel()->whereNull('deleted_at')->count();
    $status = (string) $project->status;
    $statusColors = [
        'pending' => '#e5e7eb',
        'in_progress' => '#BFDBFE',
        'done' => '#FEF08A',
        'completed' => '#BBF7D0',
    ];
    $details = [
        ['icon' => 'heroicon-o-arrow-path', 'label' => 'Status:', 'value' => null, 'badge' => $status],
        ['icon' => 'heroicon-o-flag', 'label' => 'Priority', 'value' => $project->priority ?? 'None'],
        ['icon' => 'heroicon-o-banknotes', 'label' => 'Total Paid', 'value' => number_format((float) $project->total_paid, 2)],
        ['icon' => 'heroicon-o-banknotes', 'label' => 'Total Left', 'value' => number_format((float) $project->total_left, 2)],
        ['icon' => 'heroicon-o-map-pin', 'label' => 'Location', 'value' => $project->office?->name],
        ['icon' => 'heroicon-o-building-office', 'label' => 'Directorate', 'value' => $project->directorate?->name ?? $project->department?->directorate?->name],
        ['icon' => 'heroicon-o-user-circle', 'label' => 'Contractor', 'value' => $project->contractor?->user?->name],
        ['icon' => 'heroicon-o-calendar', 'label' => 'Award date', 'value' => $project->award_date?->format('Y-m-d')],
        ['icon' => 'heroicon-o-clock', 'label' => 'Duration', 'value' => $project->duration ? $project->duration.' - '.$project->duration_period : null],
        ['icon' => 'heroicon-o-credit-card', 'label' => 'Cost', 'value' => number_format((float) $project->cost, 2)],
    ];
    $endDate = ($project->award_date && $project->duration && in_array($project->duration_period, \App\Models\Project::DURATION_PERIODS, true)) ? $project->end_date : null;
    $isExpired = $endDate && \Carbon\Carbon::now()->gt($endDate);
    $timeLeft = $endDate ? ($isExpired ? 'Expired' : \Carbon\Carbon::now()->diffForHumans($endDate, ['parts' => 3])) : 'N/A';
    $awardLetterUrl = $project->award_letter ? \Illuminate\Support\Facades\Storage::disk('public')->url($project->award_letter) : null;
@endphp

<div style="display:flex; gap:24px; align-items:flex-start; flex-wrap:wrap;">
    <div style="flex:1 1 55%; min-width:300px;">
        <div style="margin-bottom:16px;">
            <a href="{{ \App\Filament\Resources\ProjectResource::getUrl('index') }}" style="color:#5B5FC7; text-decoration:none; font-size:14px;">
                &larr; Back
            </a>
        </div>

        <h2 style="font-size:22px; font-weight:700; margin:0 0 20px; color:#111827;">
            {{ $project->name }}
        </h2>

        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; gap:16px;">
            <span style="font-size:16px; font-weight:600; color:#111827;">Project details</span>
            @if ($canSeeOrgButtons)
                <div style="display:flex; gap:8px; flex-wrap:wrap; justify-content:flex-end;">
                    @if ($progress >= 100)
                        <a href="{{ \App\Filament\Resources\ProjectResource::getUrl('report', ['record' => $project]) }}" style="background:#5B5FC7; color:white; padding:6px 16px; border-radius:20px; font-size:13px; text-decoration:none;">
                            Project Report
                        </a>
                    @endif
                    <a href="{{ \App\Filament\Resources\ProjectResource::getUrl('index') }}" style="background:#16A34A; color:white; padding:6px 16px; border-radius:20px; font-size:13px; text-decoration:none;">
                        All Projects
                    </a>
                </div>
            @endif
        </div>

        @foreach ($details as $detail)
            <div style="display:flex; justify-content:space-between; align-items:center; padding:10px 0; border-bottom:1px solid #f3f4f6; gap:16px;">
                <div style="display:flex; align-items:center; gap:8px; color:#374151;">
                    <x-filament::icon :icon="$detail['icon']" style="width:18px;height:18px;" />
                    <span>{{ $detail['label'] }}</span>
                </div>
                @if ($detail['badge'] ?? false)
                    <span style="background:{{ $statusColors[$detail['badge']] ?? '#e5e7eb' }}; padding:4px 12px; border-radius:8px; font-size:13px; color:#111827;">
                        {{ ucfirst(str_replace('_', ' ', $detail['badge'])) }}
                    </span>
                @else
                    <span style="color:#111827; text-align:right;">{{ $detail['value'] ?? '-' }}</span>
                @endif
            </div>
        @endforeach

        <div style="background:#f7f7f7; border-radius:10px; padding:16px; margin-top:16px;">
            <div style="display:flex; align-items:center; gap:8px; margin-bottom:8px; font-weight:600; color:#111827;">
                <x-filament::icon icon="heroicon-o-document-text" style="width:18px;height:18px;" />
                Description
            </div>
            <p style="color:#374151; font-size:14px; margin:0; white-space:pre-line;">{{ $project->description }}</p>
        </div>

        <div style="margin-top:16px;">
            <div style="display:flex; align-items:center; gap:8px; color:#6b7280; margin-bottom:12px;">
                <x-filament::icon icon="heroicon-o-paper-clip" style="width:16px;height:16px;" />
                <span>Attachments ({{ $project->award_letter ? 1 : 0 }})</span>
            </div>
            @if ($awardLetterUrl)
                <div style="display:inline-flex; align-items:center; gap:12px; border:1px solid #e5e7eb; border-radius:8px; padding:12px 16px;">
                    <x-filament::icon icon="heroicon-o-document-arrow-down" style="width:32px;height:32px;color:#5B5FC7;" />
                    <div>
                        <a href="{{ $awardLetterUrl }}" target="_blank" style="color:#5B5FC7; font-size:13px; text-decoration:none; font-weight:500;">
                            Download Award Letter
                        </a>
                        <p style="font-size:11px; color:#9ca3af; margin:2px 0 0;">Click to view</p>
                    </div>
                </div>
            @else
                <p style="color:#9ca3af; font-size:13px;">No attachments uploaded.</p>
            @endif
        </div>
    </div>

    <div style="flex:1 1 35%; min-width:280px;">
        <div style="display:flex; justify-content:flex-end; align-items:center; gap:8px; margin-bottom:20px;">
            <span style="color:#6b7280; font-size:13px;">Time left</span>
            <span style="background:#fde8e8; color:#111827; padding:6px 12px; border-radius:8px; font-size:13px; display:flex; align-items:center; gap:6px;">
                <x-filament::icon icon="heroicon-o-clock" style="width:14px;height:14px;" />
                {{ $timeLeft }}
            </span>
        </div>

        <div style="margin-bottom:20px;">
            <h6 style="font-weight:600; margin:0 0 12px; color:#111827;">Project gallery</h6>
            @if ($recentImages->count())
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
                    @foreach ($recentImages as $image)
                        <div style="border-radius:8px; overflow:hidden;">
                            <img src="{{ $image->url }}" style="width:100%; height:120px; object-fit:cover;" alt="Task Image">
                        </div>
                    @endforeach
                </div>
            @else
                <p style="color:#9ca3af; font-size:13px; font-style:italic;">No images uploaded yet.</p>
            @endif
        </div>

        <div x-data="{ activeTab: 'comments' }">
            <div style="display:flex; gap:0; border-bottom:2px solid #e5e7eb; margin-bottom:16px;">
                <button type="button" @click="activeTab='milestones'"
                    :style="activeTab==='milestones' ? 'border-bottom:2px solid #111827; font-weight:700; color:#111827; margin-bottom:-2px;' : 'color:#6b7280;'"
                    style="padding:10px 20px; background:none; border:none; cursor:pointer; font-size:14px; white-space:nowrap;">
                    Milestones
                </button>
                <button type="button" @click="activeTab='comments'"
                    :style="activeTab==='comments' ? 'border-bottom:2px solid #111827; font-weight:700; color:#111827; margin-bottom:-2px;' : 'color:#6b7280;'"
                    style="padding:10px 20px; background:none; border:none; cursor:pointer; font-size:14px; white-space:nowrap;">
                    Comments
                </button>
                <button type="button" @click="activeTab='personnels'"
                    :style="activeTab==='personnels' ? 'border-bottom:2px solid #111827; font-weight:700; color:#111827; margin-bottom:-2px;' : 'color:#6b7280;'"
                    style="padding:10px 20px; background:none; border:none; cursor:pointer; font-size:14px; white-space:nowrap;">
                    Personnels
                </button>
            </div>

            <div x-show="activeTab==='milestones'">
                <p style="font-size:14px; color:#111827;">
                    <span style="font-weight:600;">Milestones Completed:</span> {{ $completed }}
                </p>
                <p style="font-size:14px; color:#111827;">
                    <span style="font-weight:600;">Milestones Left:</span> {{ $remaining }}
                </p>
                <a href="{{ route('filament.admin.pages.project-milestones', ['project' => $project->ulid]) }}" style="display:inline-block; margin-top:8px; border:1px solid #16A34A; color:#16A34A; padding:6px 16px; border-radius:8px; font-size:13px; text-decoration:none;">
                    View Project Milestones
                </a>
            </div>

            <div x-show="activeTab==='comments'">
                <div style="background:#f7f7f7; border-radius:10px; padding:16px; margin-bottom:16px;">
                    <p style="font-weight:700; font-size:14px; margin:0 0 12px; color:#111827;">Comment</p>

                    @forelse ($comments as $comment)
                        <div style="margin-bottom:12px;">
                            <div style="display:flex; justify-content:space-between; gap:12px; margin-bottom:4px;">
                                <strong style="font-size:13px; color:#111827;">{{ $comment->sender?->name ?? 'Unknown' }}</strong>
                                <span style="font-size:12px; color:#9ca3af;">{{ $comment->created_at?->diffForHumans() }}</span>
                            </div>
                            <p style="font-size:13px; color:#374151; margin:0;">{{ $comment->message }}</p>
                        </div>
                        <hr style="border:none; border-top:1px solid #e5e7eb; margin:8px 0;">
                    @empty
                        <p style="color:#9ca3af; font-size:13px; margin-bottom:12px;">No comments yet.</p>
                    @endforelse

                    <form wire:submit="postComment">
                        <textarea wire:model="commentData.message" name="message" placeholder="Enter your comment here" style="width:100%; min-height:80px; border:1px solid #e5e7eb; border-radius:8px; padding:10px 12px; font-size:13px; background:#ffffff; resize:none; outline:none; box-sizing:border-box; color:#374151;"></textarea>
                        <div style="display:flex; justify-content:flex-end; align-items:center; gap:8px; margin-top:8px;">
                            <x-filament::icon icon="heroicon-o-paper-clip" style="width:16px;height:16px;color:#6b7280;cursor:pointer;" />
                            <button type="submit" style="background:#b7bbff; color:#111827; border:none; padding:6px 20px; border-radius:8px; cursor:pointer; font-size:13px; font-weight:500;">
                                Publish
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div x-show="activeTab==='personnels'">
                <p style="font-size:14px; color:#111827;">
                    <span style="font-weight:600;">Project Personnels:</span> {{ $personnelCount }}
                </p>
                @if ($canManagePersonnel)
                    <a href="{{ route('filament.admin.pages.project-personnels', ['project' => $project->ulid]) }}" style="display:inline-block; margin-top:8px; border:1px solid #5B5FC7; color:#5B5FC7; padding:6px 16px; border-radius:8px; font-size:13px; text-decoration:none;">
                        View Project Personnels
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
</x-filament-panels::page>
