<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Product;
use App\Models\Project;
use App\Models\SupportTicket;
use App\Models\User;
use App\Services\ClientService;
use App\Services\ProjectDocumentService;
use App\Services\ProjectService;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $clientService = app(ClientService::class);
        $projectService = app(ProjectService::class);
        $projectDocumentService = app(ProjectDocumentService::class);
        $adminId = User::role('admin')->value('id');

        $clients = [];
        foreach ($this->clientsData() as $key => $data) {
            $clients[$key] = $clientService->createClient([
                ...$data,
                'password' => 'password',
                'country' => 'India',
            ]);
        }

        $localPulse = Product::where('name', 'LocalPulse')->firstOrFail();
        $restaurantPos = Product::where('name', 'Restaurant POS')->firstOrFail();
        $schoolErp = Product::where('name', 'School ERP')->firstOrFail();
        $restaurantRevenue = Product::where('name', 'Restaurant Revenue')->firstOrFail();
        $realEstate = Product::where('name', 'Real Estate')->firstOrFail();
        $rapidRetail = Product::where('name', 'Rapid Retail')->firstOrFail();

        $projectsData = [
            ['name' => 'AajTak City', 'product' => $localPulse, 'client' => $clients['aajtak'], 'stage' => 'live', 'docs' => 'all', 'training' => 'all'],
            ['name' => 'Samachar City', 'product' => $localPulse, 'client' => $clients['abc_media'], 'stage' => 'development', 'docs' => 'most', 'training' => 'some'],
            ['name' => 'Saurashtra Bhumi', 'product' => $localPulse, 'client' => $clients['abc_media'], 'stage' => 'documents', 'docs' => 'few', 'training' => 'none'],
            ['name' => 'SSB Travel Updates', 'product' => $localPulse, 'client' => $clients['ssb_travel'], 'stage' => 'live', 'docs' => 'all', 'training' => 'all'],
            ['name' => 'ABC School', 'product' => $schoolErp, 'client' => $clients['abc_school'], 'stage' => 'development', 'docs' => 'most', 'training' => 'few'],
            ['name' => 'XYZ School Portal', 'product' => $schoolErp, 'client' => $clients['xyz_education'], 'stage' => 'documents', 'docs' => 'few', 'training' => 'none'],
            ['name' => 'Hotel Krishna', 'product' => $restaurantPos, 'client' => $clients['hotel_krishna'], 'stage' => 'development', 'docs' => 'few', 'training' => 'none', 'blocked' => true, 'tickets' => 1],
            ['name' => 'Raj Restaurant', 'product' => $restaurantPos, 'client' => $clients['raj_restaurant'], 'stage' => 'training', 'docs' => 'most', 'training' => 'some', 'tickets' => 3],

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
        ];

        foreach ($projectsData as $data) {
            $project = $projectService->createProject([
                'product_id' => $data['product']->id,
                'client_id' => $data['client']->id,
                'project_name' => $data['name'],
                'expected_live_date' => now()->addMonths(1),
            ]);

            $this->markProgress($projectDocumentService, $adminId, $project, $data['docs'], $data['training']);

            $projectService->advanceStage($project, $data['stage']);

            if ($data['blocked'] ?? false) {
                $projectService->toggleBlocked($project);
            }

            for ($i = 0; $i < ($data['tickets'] ?? 0); $i++) {
                SupportTicket::create([
                    'project_id' => $project->id,
                    'ticket_no' => 'TKT-' . $project->id . '-' . ($i + 1),
                    'subject' => 'Client raised a question during onboarding',
                    'priority' => 'normal',
                    'status' => 'open',
                    'created_by' => $data['client']->user_id,
                ]);
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
            'training' => ['docs' => 'most', 'training' => 'some'],
            'live' => ['docs' => 'all', 'training' => 'all'],
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
