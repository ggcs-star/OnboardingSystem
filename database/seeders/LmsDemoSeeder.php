<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\LmsCategory;
use App\Models\LmsProduct;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Demo content for the LMS module — two products (Localpulse, Rapid Retail),
 * each with a mix of categories with and without sub-categories, articles
 * containing headings, images, code blocks and links. Safe to re-run:
 * existing categories/articles are matched by slug and left untouched,
 * new ones are appended.
 */
class LmsDemoSeeder extends Seeder
{
    public function run(): void
    {
        $localpulse = LmsProduct::firstOrCreate(
            ['slug' => 'localpulse'],
            [
                'name' => 'Localpulse',
                'tagline' => 'Hyperlocal News & Media Platform',
                'description' => 'Documentation for publishing, managing and monetizing local news content on Localpulse.',
                'active' => true,
            ]
        );

        $this->seedLocalpulse($localpulse);

        $rapidRetail = LmsProduct::firstOrCreate(
            ['slug' => 'rapid-retail'],
            [
                'name' => 'Rapid Retail',
                'tagline' => 'Multi-Store Retail Management System',
                'description' => 'Documentation for setting up billing, inventory and payments across every Rapid Retail store.',
                'active' => true,
            ]
        );

        $this->seedRapidRetail($rapidRetail);

        $this->assignDemoClients($localpulse, $rapidRetail);
    }

    private function seedLocalpulse(LmsProduct $product): void
    {
        $gettingStarted = $this->category($product, 'Getting Started', 'Everything you need before your first news article goes live.');

        $this->article($gettingStarted, null, 'Installation & Setup', 'Install the Localpulse CLI and scaffold your first workspace.', <<<HTML
            <h2>Prerequisites</h2>
            <p>Before you install Localpulse, make sure the following are available on your server:</p>
            <ul>
                <li>PHP 8.1 or higher</li>
                <li>MySQL 8 or MariaDB 10.6+</li>
                <li>Composer &amp; Node.js 18+</li>
            </ul>
            <p>Verify your installed versions with:</p>
            <pre class="language-bash"><code>$ php --version
PHP 8.2.4

$ composer --version
Composer version 2.6.5</code></pre>
            <h2>Create the project directory</h2>
            <p>Run the following command to set up a fresh Localpulse workspace:</p>
            <pre class="language-bash"><code>$ localpulse init my-newsroom</code></pre>
            <p>This creates a directory named <code>my-newsroom</code> with the following structure:</p>
            <pre><code>.
├── config
│   ├── app.php
│   └── database.php
├── storage
│   ├── logs
│   └── uploads
├── public
│   └── index.php
└── .env</code></pre>
            {$this->img('Localpulse installation wizard', [37, 99, 235])}
            <p>Once setup completes, open your browser and log in with the admin credentials printed in the terminal. For a deeper walkthrough, see the <a href="https://example.com/localpulse/setup" target="_blank" rel="noopener">official setup guide</a>.</p>
HTML);

        $this->article($gettingStarted, null, 'First Login & Dashboard Tour', 'A quick tour of the admin dashboard after your first login.', <<<HTML
            <h2>Logging in for the first time</h2>
            <p>Navigate to <code>https://yourdomain.com/admin</code> and sign in using the email and password you set during installation.</p>
            {$this->img('Localpulse dashboard overview', [16, 185, 129])}
            <p>The dashboard gives you an at-a-glance view of published articles, active reporters, and today's traffic. Use the left sidebar to jump between <strong>News</strong>, <strong>Reporters</strong> and <strong>Settings</strong>.</p>
HTML);

        $guides = $this->category($product, 'Guides', 'Step-by-step guides for day-to-day publishing and automation.');
        $basics = $this->subCategory($guides, 'Basics', 'Core publishing workflow for reporters and editors.');
        $advanced = $this->subCategory($guides, 'Advanced', 'Automation and integrations for power users.');

        $this->article($guides, $basics, 'Publishing Your First Article', 'Write, categorize and publish a news article end to end.', <<<HTML
            <h2>Create a draft</h2>
            <p>Go to <strong>News → Add News</strong>, fill in the title, body and a category, then click <strong>Save Draft</strong>.</p>
            {$this->img('Localpulse article editor', [234, 88, 12])}
            <h2>Publish it</h2>
            <p>Once the draft looks ready, click <strong>Publish</strong> to make it live immediately, or schedule it for a later time from the same screen.</p>
HTML);

        $this->article($guides, $advanced, 'Automating Category Feeds', 'Auto-publish an RSS feed into a category using a scheduled job.', <<<HTML
            <h2>Feed configuration</h2>
            <p>Localpulse can auto-publish RSS feeds into a category. Add an entry to <code>config/feeds.json</code>:</p>
            <pre class="language-json"><code>{
  "category": "sports",
  "source": "https://example.com/sports/rss",
  "poll_interval_minutes": 15,
  "auto_publish": true
}</code></pre>
            <p>Restart the queue worker for the change to take effect:</p>
            <pre class="language-bash"><code>$ php artisan queue:restart</code></pre>
            <p>Read more in the <a href="https://example.com/localpulse/feeds" target="_blank" rel="noopener">feeds documentation</a>.</p>
HTML, published: false);

        $apiReference = $this->category($product, 'API Reference', 'Integrate external systems with the Localpulse REST API.');

        $this->article($apiReference, null, 'REST API — Articles Endpoint', 'Authenticate and fetch published articles via the API.', <<<HTML
            <h2>Authentication</h2>
            <p>All API requests require a Bearer token, generated from <strong>Settings → API Keys</strong>.</p>
            <pre class="language-bash"><code>curl -X GET https://yourdomain.com/api/v1/articles \
  -H "Authorization: Bearer YOUR_API_TOKEN"</code></pre>
            <h2>Example response</h2>
            <pre class="language-json"><code>{
  "data": [
    { "id": 101, "title": "Local elections wrap up", "category": "politics" },
    { "id": 102, "title": "Monsoon update for the city", "category": "weather" }
  ],
  "meta": { "page": 1, "total": 42 }
}</code></pre>
            <p>The full endpoint reference is available in the <a href="https://example.com/localpulse/api" target="_blank" rel="noopener">API portal</a>.</p>
HTML);
    }

    private function seedRapidRetail(LmsProduct $product): void
    {
        $gettingStarted = $this->category($product, 'Getting Started', 'Install the POS and set up your first store.');

        $this->article($gettingStarted, null, 'Installing Rapid Retail POS', 'System requirements and first-time store setup.', <<<HTML
            <h2>System requirements</h2>
            <ul>
                <li>Windows 10+ or any modern browser for the web POS</li>
                <li>Internet connection (offline sales sync automatically later)</li>
                <li>A receipt printer (optional, ESC/POS compatible)</li>
            </ul>
            <pre class="language-bash"><code>$ rapidretail --version
RapidRetail CLI 3.2.0</code></pre>
            <p>Set up your first store:</p>
            <pre class="language-bash"><code>$ rapidretail store:init "Downtown Branch"</code></pre>
            <pre><code>.
├── store.config.json
├── inventory/
│   └── seed-products.csv
└── receipts/
    └── template.html</code></pre>
            {$this->img('Rapid Retail store setup wizard', [220, 38, 38])}
            <p>See the <a href="https://example.com/rapidretail/install" target="_blank" rel="noopener">full installation guide</a> for hardware pairing steps.</p>
HTML);

        $this->article($gettingStarted, null, 'Your First Sale', 'Ring up a sale and print a receipt from the POS screen.', <<<HTML
            <h2>Ring up an item</h2>
            <p>Scan a barcode or search a product by name, then tap it to add it to the cart.</p>
            {$this->img('Rapid Retail POS checkout screen', [8, 145, 178])}
            <h2>Take payment</h2>
            <p>Choose <strong>Cash</strong>, <strong>Card</strong> or <strong>UPI</strong>, confirm the amount, and the receipt prints automatically if a printer is connected.</p>
HTML);

        $storeSetup = $this->category($product, 'Store Setup', 'Configure inventory and payments for each store.');
        $inventory = $this->subCategory($storeSetup, 'Inventory', 'Add and manage stock across outlets.');
        $payments = $this->subCategory($storeSetup, 'Payments', 'Connect a payment gateway and handle webhooks.');

        $this->article($storeSetup, $inventory, 'Adding Products & Stock', 'Bulk import products with a CSV file.', <<<HTML
            <h2>Bulk import via CSV</h2>
            <p>Upload a CSV with the following columns to add products in bulk:</p>
            <pre class="language-csv"><code>sku,name,price,quantity,category
SKU-1001,Basmati Rice 5kg,450,120,Grocery
SKU-1002,Sunflower Oil 1L,180,80,Grocery</code></pre>
            {$this->img('Rapid Retail inventory import screen', [202, 138, 4])}
            <p>Once imported, stock levels sync to every terminal in the store within a few seconds.</p>
HTML);

        $this->article($storeSetup, $payments, 'Connecting a Payment Gateway', 'Point your gateway webhook at Rapid Retail and verify the payload.', <<<HTML
            <h2>Webhook URL</h2>
            <p>Configure your gateway's webhook to point at:</p>
            <pre class="language-bash"><code>https://yourdomain.com/webhooks/payments</code></pre>
            <p>Rapid Retail expects the following payload shape:</p>
            <pre class="language-json"><code>{
  "transaction_id": "txn_8f3a2",
  "amount": 45000,
  "currency": "INR",
  "status": "success",
  "store_id": "downtown-branch"
}</code></pre>
            <p>Refer to your gateway's dashboard (e.g. <a href="https://razorpay.com/docs/webhooks/" target="_blank" rel="noopener">Razorpay Webhooks</a>) to copy the correct signing secret.</p>
HTML);

        $apiReference = $this->category($product, 'API Reference', 'Query inventory and sales programmatically.');

        $this->article($apiReference, null, 'Inventory API', 'Fetch live stock levels for a store via the API.', <<<HTML
            <h2>Authentication</h2>
            <p>Pass your store's API key as a Bearer token on every request.</p>
            <pre class="language-bash"><code>curl -X GET https://yourdomain.com/api/v1/inventory \
  -H "Authorization: Bearer YOUR_STORE_API_KEY"</code></pre>
            <h2>Example response</h2>
            <pre class="language-json"><code>{
  "data": [
    { "sku": "SKU-1001", "name": "Basmati Rice 5kg", "quantity": 118 },
    { "sku": "SKU-1002", "name": "Sunflower Oil 1L", "quantity": 76 }
  ]
}</code></pre>
HTML);

        $troubleshooting = $this->category($product, 'Troubleshooting', 'Fixes for the issues stores run into most often.');

        $this->article($troubleshooting, null, 'Common Issues', 'Printer, sync and login problems and how to resolve them.', <<<HTML
            <h2>Printer not detected</h2>
            <p>Make sure the receipt printer is powered on and connected via USB before starting the POS app. Restart the POS if it was plugged in after launch.</p>
            <h2>Sync stuck on "Pending"</h2>
            <p>Check your internet connection — offline sales queue locally and sync automatically once connectivity is restored. You can force a retry from <strong>Settings → Sync Now</strong>.</p>
            <h2>Cashier can't log in</h2>
            <p>Confirm the cashier's PIN hasn't expired under <strong>Staff → Manage Cashiers</strong>, and that the store isn't in offline mode (PIN sync requires connectivity).</p>
HTML);
    }

    private function category(LmsProduct $product, string $name, ?string $description = null): LmsCategory
    {
        return $product->categories()->firstOrCreate(
            ['slug' => Str::slug($name)],
            ['name' => $name, 'description' => $description]
        );
    }

    private function subCategory(LmsCategory $category, string $name, ?string $description = null)
    {
        return $category->subCategories()->firstOrCreate(
            ['slug' => Str::slug($name)],
            ['name' => $name, 'description' => $description]
        );
    }

    private function article(LmsCategory $category, $subCategory, string $title, string $excerpt, string $content, bool $published = true): void
    {
        $category->product->articles()->firstOrCreate(
            ['slug' => Str::slug($title)],
            [
                'lms_category_id' => $category->id,
                'lms_sub_category_id' => $subCategory?->id,
                'title' => $title,
                'excerpt' => $excerpt,
                'content' => $content,
                'is_published' => $published,
            ]
        );
    }

    /** Renders an <img> tag, generating and storing a labelled placeholder PNG the first time it's needed. */
    private function img(string $label, array $rgb): string
    {
        $path = 'lms-articles/demo/' . Str::slug($label) . '.png';

        if (! Storage::disk('public')->exists($path)) {
            Storage::disk('public')->put($path, $this->generatePlaceholderPng($label, $rgb));
        }

        return '<img src="' . asset('storage/' . $path) . '" alt="' . e($label) . '" />';
    }

    private function generatePlaceholderPng(string $label, array $rgb): string
    {
        $width = 960;
        $height = 480;

        $image = imagecreatetruecolor($width, $height);
        $background = imagecolorallocate($image, ...$rgb);
        imagefill($image, 0, 0, $background);

        $white = imagecolorallocate($image, 255, 255, 255);
        $font = 5;
        $textWidth = imagefontwidth($font) * strlen($label);
        $x = max(20, intdiv($width - $textWidth, 2));
        $y = intdiv($height, 2) - 10;
        imagestring($image, $font, $x, $y, $label, $white);

        ob_start();
        imagepng($image);
        $data = ob_get_clean();
        imagedestroy($image);

        return $data;
    }

    private function assignDemoClients(LmsProduct $localpulse, LmsProduct $rapidRetail): void
    {
        $abcMedia = Client::where('company_name', 'ABC Media Pvt Ltd')->first();

        if ($abcMedia) {
            $localpulse->clientProducts()->firstOrCreate(
                ['client_id' => $abcMedia->id],
                ['status' => true, 'assigned_at' => now()]
            );
            $rapidRetail->clientProducts()->firstOrCreate(
                ['client_id' => $abcMedia->id],
                ['status' => true, 'assigned_at' => now()]
            );
        }

        $rapidRetailsMart = Client::where('company_name', 'Rapid Retails Mart')->first();

        if ($rapidRetailsMart) {
            $rapidRetail->clientProducts()->firstOrCreate(
                ['client_id' => $rapidRetailsMart->id],
                ['status' => true, 'assigned_at' => now()]
            );
        }
    }
}
