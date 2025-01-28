<?php

namespace App\Services;

use App\Models\Purchases\PurchaseReceipt;
use App\Models\Purchases\PurchaseReceiptLines;
use App\Models\Purchases\PurchaseLines;
use App\Models\Planning\Status;
use App\Models\Planning\Task;
use App\Events\PurchaseReceiptCreated;

class PurchaseReceiptService
{
    protected $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function createPurchaseReceipt($data, $receiptData)
    {
        // Vérifier si des lignes existent
        $i = 0;
        foreach ($data as $item) {
            if (isset($item['purchase_line_id']) && $item['purchase_line_id'] != false) {
                $i++;
            }
        }

        if ($i > 0) {
            // Récupérer le statut "Finished"
            $StatusUpdate = Status::where('title', 'Finished')->first();
            if (is_null($StatusUpdate)) {
                throw new \Exception('No status in kanban for defined finished task');
            }

            // Créer le reçu d'achat
            $ReceiptCreated = PurchaseReceipt::create($receiptData);

            // Création des lignes du reçu
            $ordre = 10; // ordre de démarrage par défaut
            foreach ($data as $key => $item) {
                $PurchaseLines = PurchaseLines::find($key);

                if ($PurchaseLines) {
                    // Créer les lignes du reçu
                    $ReceiptLines = PurchaseReceiptLines::create([
                        'purchase_receipt_id' => $ReceiptCreated->id,
                        'purchase_line_id' => $PurchaseLines->id,
                        'ordre' => $ordre,
                        'receipt_qty' => $PurchaseLines->qty,
                    ]);

                    // Mettre à jour la quantité reçue dans les lignes de commande
                    $PurchaseLines->update(['receipt_qty' => $PurchaseLines->qty]);

                    // Mise à jour du statut de la tâche
                    if ($StatusUpdate->id) {
                        Task::where('id', $PurchaseLines->tasks_id)
                            ->update(['status_id' => $StatusUpdate->id]);
                    }

                    // Enregistrer l'activité de la tâche
                    $this->taskService->recordTaskActivity($PurchaseLines->tasks_id, 4, $PurchaseLines->qty, 0);

                    // Incrémenter l'ordre pour la prochaine ligne
                    $ordre += 10;
                }
            }

            // Émettre un événement pour mettre à jour le statut d'achat
            event(new PurchaseReceiptCreated($ReceiptCreated));

            return $ReceiptCreated;
        }

        throw new \Exception('No lines selected');
    }
}
