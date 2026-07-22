<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductSubscriptionPlan;
use App\Models\Project;
use App\Models\SalesEmployee;
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

        $projectsByProduct = $client ? $client->projects->groupBy('product_id') : collect();

        $products = Product::where('active', true)->orderBy('name')->get();

        return view('client.onboarding.index', [
            'client' => $client,
            'products' => $products,
            'projectsByProduct' => $projectsByProduct,
        ]);
    }

    public function store(Request $request, Product $product): RedirectResponse
    {
        $client = $request->user()->client;

        abort_unless($client, 403);

        $data = $request->validate([
            'brand_name' => ['required', 'string', 'max:255'],
        ]);

        $brandName = trim($data['brand_name']);
        $normalizedBrandName = strtolower(str_replace(' ', '', $brandName));

        $duplicate = $client->projects()
            ->where('product_id', $product->id)
            ->whereRaw("LOWER(REPLACE(brand_name, ' ', '')) = ?", [$normalizedBrandName])
            ->exists();

        if ($duplicate) {
            return back()
                ->withErrors(['brand_name' => 'This brand name already exists for this product.'])
                ->withInput()
                ->with('duplicate_product_id', $product->id);
        }

        $project = $this->projectService->createProject([
            'client_id' => $client->id,
            'product_id' => $product->id,
            'project_name' => $product->name,
            'brand_name' => $brandName,
        ]);

        return redirect()->route('client.onboarding.documents', $project);
    }

    public function documents(Request $request, Project $project): View
    {
        abort_unless($project->client->user_id === $request->user()->id, 403);

        $project->load(['product', 'salesEmployee']);

        return view('client.onboarding.documents', [
            'project' => $project,
            'mandatoryProgress' => $project->mandatoryDocumentsProgress(),
        ]);
    }

    public function subscription(Request $request, Project $project): View|RedirectResponse
    {
        abort_unless($project->client->user_id === $request->user()->id, 403);

        [$mandatoryDone, $mandatoryTotal] = $project->mandatoryDocumentsProgress();

        if ($mandatoryDone !== $mandatoryTotal) {
            return redirect()->route('client.onboarding.documents', $project)
                ->with('error', 'Please submit all mandatory documents before continuing to subscription setup.');
        }

        $project->load(['product.subscriptionPlans', 'renewal.history', 'salesEmployee']);

        return view('client.onboarding.subscription', [
            'project' => $project,
            'plans' => $project->product->subscriptionPlans,
            'renewal' => $project->renewal,
            'salesEmployees' => SalesEmployee::where('status', 'active')->orderBy('name')->get(),
        ]);
    }

    public function selectSalesperson(Request $request, Project $project): RedirectResponse
    {
        abort_unless($project->client->user_id === $request->user()->id, 403);

        $data = $request->validate([
            'sales_employee_id' => ['nullable', 'exists:sales_employees,id'],
        ]);

        $project->update(['sales_employee_id' => $data['sales_employee_id'] ?? null]);

        return redirect()->route('client.onboarding.subscription', $project);
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
