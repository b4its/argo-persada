<?php

namespace App\Filament\Pages\Admin;

use App\Models\User;
use App\Models\Task;
use App\Models\Pesanan;
use Filament\Forms\Components\Textarea;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use BackedEnum;

class AdminDashboard extends Page
{
    use InteractsWithActions;
    use InteractsWithForms;
    use WithPagination;
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-chart-bar';
protected static string | null $dashboardIcon = '';
    protected string $view = 'filament.pages.admin.admin-dashboard';
    protected static ?string $title = 'Leaderboard & Progress Kerja';

    /**
     * #[Computed] memastikan data ini hanya di-query saat dipanggil di Blade
     * dan tidak ikut ter-serialize ke payload frontend Livewire.
     */
    #[Computed]
    public function performers(): Collection
    {
        return User::query()
            ->select(['id', 'name', 'email'])
            ->withCount([
                'createdTaskActivities as total_tasks_handled',
                'createdTaskActivities as completed_tasks' => function ($query) {
                    $query->where('pesanan_status', '>=', 3);
                }
            ])
            ->orderByDesc('completed_tasks')
            ->limit(5)
            ->get();
    }

    #[Computed]
    public function stats(): array
    {
        return [
            'total_pesanan'   => Pesanan::count(),
            'active_tasks'    => Task::where('status', '!=', 1)->count(),
            'completed_tasks' => Task::where('status', '>=', 1)->count(),
        ];
    }

    #[Computed]
    public function roleProgressData(): LengthAwarePaginator
    {
        // 1. Ambil daftar role unik
        $allRoles = Task::select('role')->distinct()->whereNotNull('role')->pluck('role');
        
        $currentPage = $this->getPage();
        $perPage = 3;

        $currentRoleSlice = $allRoles->forPage($currentPage, $perPage);
        $roleProgress = [];

        // 2. Optimasi N+1: Tarik semua task dan relasi pesanannya SEKALIGUS untuk role di halaman ini
        if ($currentRoleSlice->isNotEmpty()) {
            $tasksInRole = Task::with('pesanan:id,code,company_name,no_po')
                ->whereIn('role', $currentRoleSlice)
                ->get();

            foreach ($currentRoleSlice as $roleName) {
                // Filter dari collection memori, bukan query DB baru
                $roleTasks = $tasksInRole->where('role', $roleName);
                $total     = $roleTasks->count();
                $completed = $roleTasks->where('status', '>=', 1)->count();
                
                // Ekstrak pesanan langsung dari relasi yang sudah di-load
                $pesananList = $roleTasks->pluck('pesanan')->filter()->unique('id')->values();

                $roleProgress[] = [
                    'role'       => $roleName,
                    'total'      => $total,
                    'completed'  => $completed,
                    'percentage' => $total > 0 ? round(($completed / $total) * 100) : 0,
                    'pesanan'    => $pesananList,
                ];
            }
        }

        return new LengthAwarePaginator(
            $roleProgress,
            $allRoles->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    public function viewPesananAction(): Action
    {
        return Action::make('viewPesanan')
            ->modalHeading(fn (array $arguments) => 'Log Aktivitas — ' . (Pesanan::find($arguments['pesanan_id'])?->code ?? ''))
            ->modalWidth('5xl')
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Tutup')
            ->mountUsing(function ($form, array $arguments) {
                // Optimasi relasi saat load modal
                $pesanan = Pesanan::with([
                    'tasks' => fn ($q) => $q->select('id', 'pesanan_id', 'title', 'role', 'status'),
                    'tasks.taskActivities.createdUser:id,name'
                ])->find($arguments['pesanan_id']);

                if (!$pesanan) return;

                $form->fill([
                    'code'         => $pesanan->code,
                    'company_name' => $pesanan->company_name,
                    'tasks'        => $pesanan->tasks->map(fn ($task) => [
                        'title'      => $task->title,
                        'role'       => $task->role,
                        'status'     => $task->status == 1 ? 'SELESAI' : 'PROSES',
                        'activities' => $task->taskActivities->map(fn ($act) => [
                            'user_name'  => $act->createdUser?->name ?? 'System',
                            'note'       => $act->note,
                            'created_at' => $act->created_at,
                        ])->toArray(),
                    ])->toArray(),
                ]);
            })
            ->form([
                Grid::make(2)->schema([
                    TextInput::make('code')->label('Nomor Pesanan')->disabled(),
                    TextInput::make('company_name')->label('Nama Perusahaan')->disabled(),
                ]),
                Section::make('Log Kerja')
                    ->schema([
                        Repeater::make('tasks')
                            ->schema([
                                Grid::make(3)->schema([
                                    TextInput::make('title')->label('Tugas')->disabled(),
                                    TextInput::make('role')->label('Role')->disabled(),
                                    TextInput::make('status')->label('Status')->disabled(),
                                ]),
                                Repeater::make('activities')
                                    ->schema([
                                        Grid::make(3)->schema([
                                            TextInput::make('user_name')->label('User')->disabled(),
                                            DateTimePicker::make('created_at')->label('Waktu')->disabled(),
                                            Textarea::make('note')->label('Ket')->rows(3)->disabled(),
                                        ]),
                                    ])
                                    ->addable(false)
                                    ->deletable(false)
                                    ->compact(),
                            ])
                            ->addable(false)
                            ->deletable(false)
                            ->collapsible(),
                    ]),
            ]);
    }
}
