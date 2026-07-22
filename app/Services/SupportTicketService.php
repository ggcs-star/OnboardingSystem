<?php

namespace App\Services;

use App\Models\Project;
use App\Models\SupportTicket;
use App\Models\TicketMessage;

class SupportTicketService
{
    public function createTicket(Project $project, int $userId, array $data): SupportTicket
    {
        $ticket = $project->tickets()->create([
            'ticket_no' => 'TEMP',
            'category' => $data['category'],
            'subject' => SupportTicket::CATEGORIES[$data['category']] ?? 'Support Request',
            'status' => 'open',
            'created_by' => $userId,
        ]);

        $ticket->update(['ticket_no' => 'SUP-' . str_pad((string) $ticket->id, 5, '0', STR_PAD_LEFT)]);

        $this->addMessage($ticket, $userId, $data['description']);

        return $ticket;
    }

    public function addMessage(SupportTicket $ticket, int $userId, string $message): TicketMessage
    {
        return $ticket->messages()->create([
            'sender_id' => $userId,
            'message' => $message,
        ]);
    }
}
