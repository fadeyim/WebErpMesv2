<?php

namespace App\Services;

use App\Events\TaskActivityTriggered;
use Carbon\Carbon;
use App\Models\Planning\Task;
use App\Events\TaskChangeStatu;
use App\Models\Planning\Status;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use App\Models\Planning\TaskActivities;

class TaskService
{
    public function closeTasks($orderLineId)
    {
        // Récupérer l'ID du statut "Finished"
        $statusUpdate = Status::select('id')->where('title', 'Finished')->first();

        if ($statusUpdate) {
            // Mettre à jour les tâches de la ligne de commande
            $tasks = Task::where('order_lines_id', $orderLineId)->get();

            foreach ($tasks as $task) {
                $task->update(['status_id' => $statusUpdate->id]);

                // Enregistrer une activité de fermeture
                $this->recordTaskActivity($task->id, 3, 0, 0);

                // Déclencher un événement pour notifier le changement de statut
                Event::dispatch(new TaskChangeStatu($task->id));
            }
        }
    }

    public function recordTaskActivity($taskId, $type, $goodQty, $addBadQt)
    {
        $taskActivity = TaskActivities::create([
            'task_id' => $taskId,
            'user_id'=> Auth::user()->id,
            'type' => $type,
            'timestamp' => Carbon::now(),
            'good_qt'=> $goodQty,
            'bad_qt'=> $addBadQt,
            'comment' => '',
        ]);

        broadcast(new TaskActivityTriggered($taskActivity));
    }
}
