<?php

namespace App\Models;

use App\Models\Concerns\HasUlid;
use App\Models\Concerns\HasUserAudits;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, HasUlid, HasUserAudits, SoftDeletes;

    public const STATUSES = ['pending', 'in_progress', 'done', 'completed'];

    public const DURATION_PERIODS = ['months', 'weeks', 'days'];

    public const PROJECT_TYPES = ['Works', 'Goods', 'Services'];

    protected $fillable = [
        'ulid',
        'name',
        'status',
        'cost',
        'total_paid',
        'total_left',
        'award_date',
        'duration',
        'duration_period',
        'award_letter',
        'office_id',
        'directorate_id',
        'department_id',
        'contractor_id',
        'consultant_id',
        'priority',
        'description',
        'project_type',
        'created_by_id',
        'deleted_by',
    ];

    protected $casts = [
        'award_date' => 'date',
        'cost' => 'decimal:2',
        'total_paid' => 'decimal:2',
        'total_left' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saving(function (Project $project): void {
            $project->total_left = max(0, (float) $project->cost - (float) $project->total_paid);
        });
    }

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function directorate(): BelongsTo
    {
        return $this->belongsTo(Directorate::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function contractor(): BelongsTo
    {
        return $this->belongsTo(Contractor::class, 'contractor_id');
    }

    public function consultant(): BelongsTo
    {
        return $this->belongsTo(Contractor::class, 'consultant_id');
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(Milestone::class);
    }

    public function projectPersonnel(): HasMany
    {
        return $this->hasMany(ProjectPersonnel::class);
    }

    public function chats(): HasMany
    {
        return $this->hasMany(ProjectChat::class);
    }

    public function recentTaskImages(int $limit = 5)
    {
        return TaskImage::query()
            ->with(['uploader', 'task'])
            ->whereHas('task', fn ($task) => $task
                ->whereNull('deleted_at')
                ->whereHas('milestone', fn ($milestone) => $milestone
                    ->where('project_id', $this->id)
                    ->whereNull('deleted_at')))
            ->whereNull('deleted_at')
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function getEndDateAttribute(): Carbon
    {
        if (! $this->award_date || ! $this->duration || ! in_array($this->duration_period, self::DURATION_PERIODS, true)) {
            return Carbon::now();
        }

        $end = $this->award_date->copy();

        return match ($this->duration_period) {
            'months' => $end->addMonths($this->duration),
            'weeks'  => $end->addWeeks($this->duration),
            'days'   => $end->addDays($this->duration),
            default  => $end,
        };
    }

    public function getMilestoneAmountUsedAttribute(): float
    {
        return (float) $this->milestones()->whereNull('deleted_at')->sum('amount');
    }

    public function getMilestoneAmountRemainingAttribute(): float
    {
        return max(0.0, (float) $this->cost - $this->milestone_amount_used);
    }

    public function hasMilestones(): bool
    {
        return $this->milestones()->whereNull('deleted_at')->exists();
    }

    public function hasTasks(): bool
    {
        return Task::query()
            ->whereNull('deleted_at')
            ->whereHas('milestone', fn ($q) => $q
                ->where('project_id', $this->id)
                ->whereNull('deleted_at'))
            ->exists();
    }

    public function get_progress(): int
    {
        $tasks = Task::query()
            ->whereHas('milestone', fn ($query) => $query->where('project_id', $this->id))
            ->whereNull('deleted_at');

        $total = (clone $tasks)->count();

        if ($total === 0) {
            return 0;
        }

        $done = $tasks->where('status', 'done')->count();

        return (int) round(($done / $total) * 100);
    }

    protected function timeLeft(): Attribute
    {
        return Attribute::get(function (): ?string {
            if (! $this->award_date || ! $this->duration || ! in_array($this->duration_period, self::DURATION_PERIODS, true)) {
                return null;
            }

            $endDate = $this->award_date->copy();

            match ($this->duration_period) {
                'months' => $endDate->addMonths($this->duration),
                'weeks' => $endDate->addWeeks($this->duration),
                'days' => $endDate->addDays($this->duration),
            };

            if ($endDate->isPast()) {
                return 'Expired';
            }

            return now()->diffForHumans($endDate, CarbonInterface::DIFF_ABSOLUTE, false, 2);
        });
    }
}
