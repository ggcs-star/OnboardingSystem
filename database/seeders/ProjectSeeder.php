<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Project;
use App\Models\SalesEmployee;
use App\Models\User;
use App\Services\ClientService;
use App\Services\ProjectDocumentService;
use App\Services\ProjectService;
use App\Services\RenewalService;
use App\Services\SupportTicketService;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $clientService = app(ClientService::class);
        $projectService = app(ProjectService::class);
        $projectDocumentService = app(ProjectDocumentService::class);
        $renewalService = app(RenewalService::class);
        $supportTicketService = app(SupportTicketService::class);
        $adminId = User::role('admin')->value('id');

        $clients = [];
        foreach ($this->clientsData() as $key => $data) {
            $clients[$key] = $clientService->createClient([
                ...$data,
                'password' => 'password',
                'country' => 'India',
            ]);
        }

        $salespeople = SalesEmployee::pluck('id', 'name');

        $localPulse = Product::where('name', 'LocalPulse')->firstOrFail();
        $restaurantPos = Product::where('name', 'Restaurant POS')->firstOrFail();
        $schoolErp = Product::where('name', 'School ERP')->firstOrFail();
        $restaurantRevenue = Product::where('name', 'Restaurant Revenue')->firstOrFail();
        $realEstate = Product::where('name', 'Real Estate')->firstOrFail();
        $rapidRetail = Product::where('name', 'Rapid Retail')->firstOrFail();

        $projectsData = [
            ['name' => 'AajTak City', 'product' => $localPulse, 'client' => $clients['aajtak'], 'stage' => 'application_live', 'docs' => 'all', 'training' => 'all', 'sales' => 'Joydeep'],
            ['name' => 'Samachar City', 'product' => $localPulse, 'client' => $clients['abc_media'], 'stage' => 'development', 'docs' => 'most', 'training' => 'some'],
            ['name' => 'Saurashtra Bhumi', 'product' => $localPulse, 'client' => $clients['abc_media'], 'stage' => 'documents', 'docs' => 'few', 'training' => 'none'],
            ['name' => 'SSB Travel Updates', 'product' => $localPulse, 'client' => $clients['ssb_travel'], 'stage' => 'application_live', 'docs' => 'all', 'training' => 'all'],
            ['name' => 'ABC School', 'product' => $schoolErp, 'client' => $clients['abc_school'], 'stage' => 'development', 'docs' => 'most', 'training' => 'few'],
            ['name' => 'XYZ School Portal', 'product' => $schoolErp, 'client' => $clients['xyz_education'], 'stage' => 'documents', 'docs' => 'few', 'training' => 'none'],
            [
                'name' => 'Hotel Krishna', 'product' => $restaurantPos, 'client' => $clients['hotel_krishna'],
                'stage' => 'development', 'docs' => 'few', 'training' => 'none', 'blocked' => true, 'sales' => 'Priya Solanki',
                'tickets' => [
                    ['category' => 'complaint', 'message' => 'The document upload keeps failing for our FSSAI license PDF.', 'reply' => "Thanks for flagging this — we've increased the upload size limit, please try again.", 'status' => 'resolved'],
                ],
            ],
            [
                'name' => 'Raj Restaurant', 'product' => $restaurantPos, 'client' => $clients['raj_restaurant'],
                'stage' => 'web_live', 'docs' => 'most', 'training' => 'some', 'sales' => 'Karan Vora',
                'tickets' => [
                    ['category' => 'training', 'message' => 'Where can we watch the billing training video again?', 'reply' => 'You can find it under Training > Billing & Payments — it stays available any time.', 'status' => 'resolved'],
                    ['category' => 'help', 'message' => 'We need help setting up a second outlet.'],
                    ['category' => 'complaint', 'message' => 'Our staff account got logged out repeatedly yesterday.'],
                ],
            ],

            // Additional products/projects from the client's handwritten planning note.
            ['name' => 'Hindtimes', 'product' => $localPulse, 'client' => $clients['hind_times'], ...$this->randomProfile()],
            ['name' => 'Gujaratnews', 'product' => $localPulse, 'client' => $clients['gujarat_news'], ...$this->randomProfile()],
            ['name' => 'Saladose', 'product' => $restaurantRevenue, 'client' => $clients['saladose'], ...$this->randomProfile()],
            ['name' => 'Rathrirabites', 'product' => $restaurantRevenue, 'client' => $clients['rathrirabites'], ...$this->randomProfile()],
            ['name' => 'Keranea1', 'product' => $realEstate, 'client' => $clients['keranea1'], ...$this->randomProfile()],
            ['name' => 'Realestate', 'product' => $realEstate, 'client' => $clients['realestate_one'], ...$this->randomProfile()],
            ['name' => 'Bhoodevi', 'product' => $realEstate, 'client' => $clients['bhoodevi'], ...$this->randomProfile()],
            ['name' => 'Mahera Jewels', 'product' => $rapidRetail, 'client' => $clients['mahera_jewels'], ...$this->randomProfile()],
            ['name' => 'Rapid Retails', 'product' => $rapidRetail, 'client' => $clients['rapid_retails'], ...$this->randomProfile()],

            // Second product for the same client (ABC Media) — the fullest
            // demo scenario: multi-product client, brand-specific contact,
            // salesperson, partial payment history, a support ticket with an
            // admin reply, and a customization request.
            [
                'name' => 'ABC Retail Hub',
                'product' => $rapidRetail,
                'client' => $clients['abc_media'],
                'stage' => 'application_live',
                'docs' => 'all',
                'training' => 'all',
                'sales' => 'Joydeep',
                'contact_name' => 'Rahul Patel (Retail Division)',
                'contact_phone' => '9998887766',
                'payment' => 45000,
                'customization' => [
                    'title' => 'Add WhatsApp order notifications',
                    'description' => 'We want customers to get an automatic WhatsApp message when their order is packed.',
                    'status' => 'approved',
                    'admin_notes' => "Done — WhatsApp notifications go out automatically once an order is marked 'packed'.",
                ],
                'tickets' => [
                    ['category' => 'training', 'message' => 'Can someone walk us through the inventory reorder alerts again?', 'reply' => "Of course — it's covered in the Inventory Management video, and I've also scheduled a call for Thursday.", 'status' => 'in_progress'],
                ],
            ],
        ];

        foreach ($projectsData as $data) {
            $project = $projectService->createProject([
                'product_id' => $data['product']->id,
                'client_id' => $data['client']->id,
                'project_name' => $data['name'],
                'contact_name' => $data['contact_name'] ?? null,
                'contact_phone' => $data['contact_phone'] ?? null,
                'sales_employee_id' => isset($data['sales']) ? ($salespeople[$data['sales']] ?? null) : null,
                'expected_live_date' => now()->addMonths(1),
            ]);

            $this->markProgress($projectDocumentService, $adminId, $project, $data['docs'], $data['training']);

            $project = $projectService->advanceStage($project, $data['stage']);

            if ($data['blocked'] ?? false) {
                $projectService->toggleBlocked($project);
            }

            if (isset($data['payment']) && $project->renewal) {
                $renewalService->recordPayment(
                    $project->renewal,
                    (float) $data['payment'],
                    now()->subDays(5)->toDateString(),
                    'Bank Transfer',
                    'Initial payment'
                );
            }

            if (isset($data['customization'])) {
                $project->customizationRequests()->create([
                    'created_by' => $data['client']->user_id,
                    'title' => $data['customization']['title'],
                    'description' => $data['customization']['description'],
                    'status' => $data['customization']['status'],
                    'admin_notes' => $data['customization']['admin_notes'] ?? null,
                    'reviewed_by' => $data['customization']['status'] !== 'pending' ? $adminId : null,
                    'reviewed_at' => $data['customization']['status'] !== 'pending' ? now() : null,
                ]);
            }

            foreach ($data['tickets'] ?? [] as $ticketData) {
                $ticket = $supportTicketService->createTicket($project, $data['client']->user_id, [
                    'category' => $ticketData['category'],
                    'description' => $ticketData['message'],
                ]);

                if (isset($ticketData['reply']) && $adminId) {
                    $supportTicketService->addMessage($ticket, $adminId, $ticketData['reply']);
                }

                if (isset($ticketData['status'])) {
                    $ticket->update(['status' => $ticketData['status']]);
                }
            }
        }
    }

    /**
     * A random but plausible stage/docs/training profile for projects that
     * don't need a specific curated scenario.
     */
    private function randomProfile(): array
    {
        $stage = collect(array_keys(Project::STAGES))->random();
        $levelsByStage = [
            'documents' => ['docs' => 'few', 'training' => 'none'],
            'development' => ['docs' => 'most', 'training' => 'few'],
            'testing' => ['docs' => 'most', 'training' => 'some'],
            'web_live' => ['docs' => 'most', 'training' => 'some'],
            'application_live' => ['docs' => 'all', 'training' => 'all'],
        ];

        return ['stage' => $stage, ...$levelsByStage[$stage]];
    }

    private function markProgress(ProjectDocumentService $projectDocumentService, ?int $adminId, Project $project, string $docsLevel, string $trainingLevel): void
    {
        $entries = $project->documentEntries();
        $approveCount = match ($docsLevel) {
            'all' => $entries->count(),
            'most' => (int) ceil($entries->count() * 0.85),
            'few' => (int) floor($entries->count() * 0.3),
            default => 0,
        };

        foreach ($entries as $index => $entry) {
            if ($index < $approveCount) {
                $projectDocumentService->submit($project, $entry->group_slug, $entry->field_key, 'Sample submitted value', null);
                $projectDocumentService->review($project, $entry->group_slug, $entry->field_key, 'approved', null, $adminId ?? 0);
            } elseif ($docsLevel !== 'none' && $index === $approveCount && $approveCount < $entries->count()) {
                $projectDocumentService->submit($project, $entry->group_slug, $entry->field_key, 'Awaiting review', null);
            }
        }

        $trainingIds = $project->product->training->pluck('id');
        $progress = $project->client->trainingProgress()->whereIn('training_id', $trainingIds)->orderBy('id')->get();
        $completeCount = match ($trainingLevel) {
            'all' => $progress->count(),
            'some' => (int) ceil($progress->count() * 0.5),
            'few' => (int) floor($progress->count() * 0.2),
            default => 0,
        };

        foreach ($progress as $index => $item) {
            if ($index < $completeCount) {
                $item->update(['completed' => true, 'completed_at' => now()]);
            }
        }
    }

    /**
     * @return array<string, array{company_name: string, owner_name: string, email: string, city: string, state: string}>
     */
    private function clientsData(): array
    {
        return [
            'aajtak' => ['company_name' => 'AajTak Media Ltd', 'owner_name' => 'Priya Shah', 'email' => 'priya@aajtak.in', 'city' => 'Rajkot', 'state' => 'Gujarat'],
            'abc_media' => ['company_name' => 'ABC Media Pvt Ltd', 'owner_name' => 'Rahul Patel', 'email' => 'rahul@abcmedia.com', 'city' => 'Ahmedabad', 'state' => 'Gujarat'],
            'abc_school' => ['company_name' => 'ABC School Trust', 'owner_name' => 'Amit Joshi', 'email' => 'amit@abcschool.edu.in', 'city' => 'Anand', 'state' => 'Gujarat'],
            'hotel_krishna' => ['company_name' => 'Hotel Krishna Pvt Ltd', 'owner_name' => 'Krishna Mehta', 'email' => 'krishna@hotelkrishna.in', 'city' => 'Bhavnagar', 'state' => 'Gujarat'],
            'raj_restaurant' => ['company_name' => 'Raj Restaurant', 'owner_name' => 'Rajesh Kumar', 'email' => 'rajesh@rajrestaurant.com', 'city' => 'Vadodara', 'state' => 'Gujarat'],
            'ssb_travel' => ['company_name' => 'SSB Travel Pvt Ltd', 'owner_name' => 'Suresh Bhai', 'email' => 'suresh@ssbtravel.com', 'city' => 'Surat', 'state' => 'Gujarat'],
            'xyz_education' => ['company_name' => 'XYZ Education Society', 'owner_name' => 'Neha Trivedi', 'email' => 'neha@xyzschool.edu.in', 'city' => 'Gandhinagar', 'state' => 'Gujarat'],

            // From the client's handwritten planning note.
            'hind_times' => ['company_name' => 'Hind Times Media Pvt Ltd', 'owner_name' => 'Vikram Desai', 'email' => 'vikram@hindtimes.in', 'city' => 'Surat', 'state' => 'Gujarat'],
            'gujarat_news' => ['company_name' => 'Gujarat News Network', 'owner_name' => 'Falguni Shah', 'email' => 'falguni@gujaratnews.in', 'city' => 'Ahmedabad', 'state' => 'Gujarat'],
            'saladose' => ['company_name' => 'Saladose Foods Pvt Ltd', 'owner_name' => 'Karan Mehta', 'email' => 'karan@saladose.in', 'city' => 'Rajkot', 'state' => 'Gujarat'],
            'rathrirabites' => ['company_name' => 'Rathri Rabites Pvt Ltd', 'owner_name' => 'Ismail Sheikh', 'email' => 'ismail@rathrirabites.in', 'city' => 'Vadodara', 'state' => 'Gujarat'],
            'keranea1' => ['company_name' => 'Kerane A1 Developers', 'owner_name' => 'Bharat Kerane', 'email' => 'bharat@keranea1.in', 'city' => 'Rajkot', 'state' => 'Gujarat'],
            'realestate_one' => ['company_name' => 'RealEstate One Pvt Ltd', 'owner_name' => 'Sanjay Oza', 'email' => 'sanjay@realestateone.in', 'city' => 'Ahmedabad', 'state' => 'Gujarat'],
            'bhoodevi' => ['company_name' => 'Bhoodevi Realty', 'owner_name' => 'Devendra Rana', 'email' => 'devendra@bhoodevi.in', 'city' => 'Gandhinagar', 'state' => 'Gujarat'],
            'mahera_jewels' => ['company_name' => 'Mahera Jewels Pvt Ltd', 'owner_name' => 'Mahera Qureshi', 'email' => 'mahera@maherajewels.in', 'city' => 'Surat', 'state' => 'Gujarat'],
            'rapid_retails' => ['company_name' => 'Rapid Retails Mart', 'owner_name' => 'Naresh Gupta', 'email' => 'naresh@rapidretails.in', 'city' => 'Ahmedabad', 'state' => 'Gujarat'],
        ];
    }
}
