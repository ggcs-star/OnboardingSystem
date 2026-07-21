<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductSubscriptionPlan;
use App\Models\Project;
use App\Services\ProjectService;
use App\Services\RenewalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    public function __construct(
        private ProjectService $projectService,
        private RenewalService $renewalService,
    ) {
    }

    public function index(Request $request): View
    {
        $client = $request->user()->client;

        $onboardedProjects = $client ? $client->projects->keyBy('product_id') : collect();

        $products = Product::where('active', true)->orderBy('name')->get();

        return view('client.onboarding.index', [
            'client' => $client,
            'products' => $products,
            'onboardedProjects' => $onboardedProjects,
        ]);
    }

    public function store(Request $request, Product $product): RedirectResponse
    {
        $client = $request->user()->client;

        abort_unless($client, 403);

        $existing = $client->projects()->where('product_id', $product->id)->first();

        if ($existing) {
            return redirect()->route('client.onboarding.documents', $existing);
        }

        $project = $this->projectService->createProject([
            'client_id' => $client->id,
            'product_id' => $product->id,
            'project_name' => $product->name,
        ]);

        return redirect()->route('client.onboarding.documents', $project);
    }

    public function documents(Request $request, Project $project): View
    {
        abort_unless($project->client->user_id === $request->user()->id, 403);

        $project->load('product');

        return view('client.onboarding.documents', [
            'project' => $project,
            'mandatoryProgress' => $project->mandatoryDocumentsProgress(),
        ]);
    }

    public function subscription(Request $request, Project $project): View
    {
        abort_unless($project->client->user_id === $request->user()->id, 403);

        $project->load(['product.subscriptionPlans', 'renewal.history']);

        return view('client.onboarding.subscription', [
            'project' => $project,
            'plans' => $project->product->subscriptionPlans,
            'renewal' => $project->renewal,
        ]);
    }

    public function selectPlan(Request $request, Project $project): RedirectResponse
    {
        abort_unless($project->client->user_id === $request->user()->id, 403);

        $data = $request->validate([
            'subscription_plan_id' => [
                'required',
                Rule::exists('product_subscription_plans', 'id')->where('product_id', $project->product_id),
            ],
        ]);

        $plan = ProductSubscriptionPlan::findOrFail($data['subscription_plan_id']);

        $this->renewalService->selectPlan($project, $plan);

        return redirect()->route('client.onboarding.subscription', $project);
    }

    public function recordPayment(Request $request, Project $project): RedirectResponse
    {
        abort_unless($project->client->user_id === $request->user()->id, 403);
        abort_unless($project->renewal, 404);

        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_date' => ['required', 'date'],
            'payment_mode' => ['nullable', 'string', 'max:255'],
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->renewalService->recordPayment(
            $project->renewal,
            (float) $data['amount'],
            $data['payment_date'],
            $data['payment_mode'] ?? null,
            $data['remarks'] ?? null,
        );

        return redirect()
            ->route('client.onboarding.subscription', $project)
            ->with('success', 'Payment recorded.');
    }
}
