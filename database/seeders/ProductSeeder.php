<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductPolicy;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $localPulse = Product::create([
            'name' => 'LocalPulse',
            'tagline' => 'Hyperlocal News & Media Platform',
            'category' => 'Media & Publishing',
            'introduction' => 'LocalPulse is a complete hyperlocal news management platform that enables media companies to publish, manage, and monetize local news content. Features include multi-reporter management, category-wise news, advertisement management, and real-time analytics.',
            'active' => true,
        ]);

        $this->seedDefaultPolicies($localPulse);

        $localPulse->documentFields()->createMany([
            ['label' => 'Company Logo', 'field_key' => 'company_logo', 'field_type' => 'image', 'placeholder' => 'Upload company logo (PNG/JPG, min 200x200px)', 'required' => true],
            ['label' => 'GST Number', 'field_key' => 'gst_number', 'field_type' => 'text', 'placeholder' => 'e.g. 24ABCDE1234F1Z5', 'required' => true],
            ['label' => 'Domain Name', 'field_key' => 'domain_name', 'field_type' => 'text', 'placeholder' => 'e.g. samacharcity.in', 'required' => true],
            ['label' => 'Hosting Credentials', 'field_key' => 'hosting_credentials', 'field_type' => 'key', 'placeholder' => 'cPanel username and password', 'required' => true],
            ['label' => 'SMTP Configuration', 'field_key' => 'smtp_configuration', 'field_type' => 'key', 'placeholder' => 'Host, port, username, password', 'required' => true],
            ['label' => 'Google Map API Key', 'field_key' => 'google_map_api_key', 'field_type' => 'key', 'placeholder' => 'AIza...', 'required' => false],
            ['label' => 'Razorpay Key', 'field_key' => 'razorpay_key', 'field_type' => 'key', 'placeholder' => 'rzp_live_...', 'required' => false],
        ]);

        $localPulse->training()->createMany([
            ['title' => 'Getting Started — Login & Dashboard', 'description' => 'Learn how to login and navigate the admin dashboard.', 'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'duration' => '8:24'],
            ['title' => 'Managing Categories', 'description' => 'Create and manage news categories.', 'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'duration' => '6:15'],
            ['title' => 'Adding & Publishing News', 'description' => 'Step-by-step guide to add and publish news articles.', 'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'duration' => '12:40'],
            ['title' => 'Reporter Management', 'description' => 'Add reporters, assign categories, and manage permissions.', 'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'duration' => '9:55'],
        ]);

        $localPulse->faqs()->createMany([
            ['question' => 'How do I login to the admin panel?', 'answer' => 'Visit your domain/admin and use your registered email and password. If you forgot your password, use the "Forgot Password" option on the login page.'],
            ['question' => 'How do I add a new news article?', 'answer' => 'Go to News > Add News from the sidebar, fill in the title, content, and category, then click Publish to make it live immediately or Save Draft to review later.'],
            ['question' => 'How do I change the company logo?', 'answer' => 'Go to Settings > General Settings and upload a new logo. Recommended size is 200x60px in PNG format with transparent background.'],
            ['question' => 'How do I add a reporter account?', 'answer' => 'Go to Users > Reporters > Add Reporter, fill in their details and assign the categories they can publish to. They will receive login credentials via email.'],
            ['question' => 'What is the Google Map API key used for?', 'answer' => 'The Google Map API key is used to display location-based news on the map view of your portal. It is optional but recommended for a better user experience.'],
        ]);

        $restaurantPos = Product::create([
            'name' => 'Restaurant POS',
            'tagline' => 'Complete Point of Sale for Restaurants',
            'category' => 'Hospitality & F&B',
            'introduction' => 'Restaurant POS is a full-featured point-of-sale system designed for restaurants, hotels, and food chains, covering billing, inventory, table management, and kitchen order tickets.',
            'active' => true,
        ]);

        $this->seedDefaultPolicies($restaurantPos);

        $restaurantPos->documentFields()->createMany([
            ['label' => 'Restaurant Logo', 'field_key' => 'restaurant_logo', 'field_type' => 'image', 'placeholder' => 'Upload restaurant logo (PNG/JPG)', 'required' => true],
            ['label' => 'FSSAI License Number', 'field_key' => 'fssai_license_number', 'field_type' => 'text', 'placeholder' => 'e.g. 12345678901234', 'required' => true],
            ['label' => 'GST Number', 'field_key' => 'gst_number', 'field_type' => 'text', 'placeholder' => 'e.g. 24ABCDE1234F1Z5', 'required' => true],
            ['label' => 'Menu PDF', 'field_key' => 'menu_pdf', 'field_type' => 'pdf', 'placeholder' => 'Upload current menu as PDF', 'required' => false],
            ['label' => 'Payment Gateway Key', 'field_key' => 'payment_gateway_key', 'field_type' => 'key', 'placeholder' => 'rzp_live_...', 'required' => true],
        ]);

        $restaurantPos->training()->createMany([
            ['title' => 'Getting Started — Login & Dashboard', 'description' => 'Learn how to login and navigate the admin dashboard.', 'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'duration' => '7:10'],
            ['title' => 'Table & Order Management', 'description' => 'Create tables, take orders, and send kitchen order tickets.', 'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'duration' => '10:05'],
            ['title' => 'Menu & Inventory Setup', 'description' => 'Add menu items, categories, and manage stock.', 'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'duration' => '9:30'],
            ['title' => 'Billing & Payments', 'description' => 'Generate bills, apply discounts, and accept payments.', 'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'duration' => '6:45'],
            ['title' => 'Staff Roles & Permissions', 'description' => 'Add staff accounts and control what they can access.', 'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'duration' => '5:20'],
            ['title' => 'Reports & Analytics', 'description' => 'View daily sales, best-selling items, and revenue reports.', 'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'duration' => '8:00'],
        ]);

        $restaurantPos->faqs()->createMany([
            ['question' => 'How do I print a bill?', 'answer' => 'Open the order, click Generate Bill, and select your connected printer to print or share it digitally.'],
            ['question' => 'Can I manage multiple outlets?', 'answer' => 'Yes, go to Settings > Outlets to add and switch between multiple restaurant branches.'],
        ]);

        $schoolErp = Product::create([
            'name' => 'School ERP',
            'tagline' => 'Complete School Management System',
            'category' => 'Education',
            'introduction' => 'School ERP is a comprehensive school management system covering admissions, attendance, fee collection, exams, and parent communication in one platform.',
            'active' => true,
        ]);

        $this->seedDefaultPolicies($schoolErp);

        $schoolErp->documentFields()->createMany([
            ['label' => 'School Logo', 'field_key' => 'school_logo', 'field_type' => 'image', 'placeholder' => 'Upload school logo (PNG/JPG)', 'required' => true],
            ['label' => 'School Registration Number', 'field_key' => 'school_registration_number', 'field_type' => 'text', 'placeholder' => 'e.g. SCH/2020/00123', 'required' => true],
            ['label' => 'Domain Name', 'field_key' => 'domain_name', 'field_type' => 'text', 'placeholder' => 'e.g. myschool.edu.in', 'required' => true],
            ['label' => 'Affiliation Certificate', 'field_key' => 'affiliation_certificate', 'field_type' => 'pdf', 'placeholder' => 'Upload board affiliation certificate', 'required' => true],
            ['label' => 'Hosting Credentials', 'field_key' => 'hosting_credentials', 'field_type' => 'key', 'placeholder' => 'cPanel username and password', 'required' => true],
            ['label' => 'SMTP Configuration', 'field_key' => 'smtp_configuration', 'field_type' => 'key', 'placeholder' => 'Host, port, username, password', 'required' => true],
            ['label' => 'SMS Gateway Key', 'field_key' => 'sms_gateway_key', 'field_type' => 'key', 'placeholder' => 'API key for SMS notifications', 'required' => false],
            ['label' => 'Payment Gateway Key', 'field_key' => 'payment_gateway_key', 'field_type' => 'key', 'placeholder' => 'rzp_live_...', 'required' => true],
        ]);

        $schoolErp->training()->createMany([
            ['title' => 'Getting Started — Login & Dashboard', 'description' => 'Learn how to login and navigate the admin dashboard.', 'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'duration' => '7:45'],
            ['title' => 'Student Admissions', 'description' => 'Add new students and manage admission records.', 'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'duration' => '11:15'],
            ['title' => 'Attendance Management', 'description' => 'Mark and track daily student attendance.', 'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'duration' => '6:30'],
            ['title' => 'Fee Collection', 'description' => 'Set fee structures and collect payments online or offline.', 'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'duration' => '9:00'],
            ['title' => 'Exams & Report Cards', 'description' => 'Create exams, enter marks, and generate report cards.', 'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'duration' => '10:50'],
            ['title' => 'Timetable Management', 'description' => 'Build class and teacher timetables.', 'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'duration' => '5:40'],
            ['title' => 'Parent Communication', 'description' => 'Send notices and updates to parents via SMS and app.', 'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'duration' => '4:55'],
            ['title' => 'Reports & Analytics', 'description' => 'View academic and financial reports across the school.', 'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'duration' => '8:20'],
        ]);

        $schoolErp->faqs()->createMany([
            ['question' => 'How do I add a new student?', 'answer' => 'Go to Students > Add Student, fill in the admission details, and assign a class and section.'],
            ['question' => 'Can parents access report cards online?', 'answer' => 'Yes, parents can log in to the parent portal or app to view attendance, fees, and report cards.'],
        ]);

        $restaurantRevenue = Product::create([
            'name' => 'Restaurant Revenue',
            'tagline' => 'Restaurant Revenue & Order Management Platform',
            'category' => 'Hospitality & F&B',
            'introduction' => 'Restaurant Revenue helps multi-outlet food brands track daily sales, expenses, and profitability across every branch, with consolidated owner-level reporting.',
            'active' => true,
        ]);

        $this->seedDefaultPolicies($restaurantRevenue);

        $restaurantRevenue->documentFields()->createMany([
            ['label' => 'Brand Logo', 'field_key' => 'brand_logo', 'field_type' => 'image', 'placeholder' => 'Upload brand logo (PNG/JPG)', 'required' => true],
            ['label' => 'GST Number', 'field_key' => 'gst_number', 'field_type' => 'text', 'placeholder' => 'e.g. 24ABCDE1234F1Z5', 'required' => true],
            ['label' => 'FSSAI License Number', 'field_key' => 'fssai_license_number', 'field_type' => 'text', 'placeholder' => 'e.g. 12345678901234', 'required' => true],
            ['label' => 'Bank Account Details', 'field_key' => 'bank_account_details', 'field_type' => 'key', 'placeholder' => 'Account number, IFSC, bank name', 'required' => true],
            ['label' => 'Payment Gateway Key', 'field_key' => 'payment_gateway_key', 'field_type' => 'key', 'placeholder' => 'rzp_live_...', 'required' => false],
        ]);

        $restaurantRevenue->training()->createMany([
            ['title' => 'Getting Started — Login & Dashboard', 'description' => 'Learn how to login and navigate the revenue dashboard.', 'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'duration' => '7:30'],
            ['title' => 'Daily Sales Entry', 'description' => 'Record daily sales across outlets.', 'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'duration' => '6:10'],
            ['title' => 'Expense Tracking', 'description' => 'Log expenses and categorize them by outlet.', 'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'duration' => '5:45'],
            ['title' => 'Owner Reports & Analytics', 'description' => 'View consolidated profitability reports across all outlets.', 'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'duration' => '9:15'],
        ]);

        $restaurantRevenue->faqs()->createMany([
            ['question' => 'Can I track multiple outlets under one account?', 'answer' => 'Yes, go to Settings > Outlets to add every branch and switch between them from the dashboard.'],
            ['question' => 'How is profitability calculated?', 'answer' => 'Profitability is daily sales minus logged expenses, shown per outlet and consolidated across your brand.'],
        ]);

        $realEstate = Product::create([
            'name' => 'Real Estate',
            'tagline' => 'Real Estate Listing & CRM Platform',
            'category' => 'Real Estate',
            'introduction' => 'Real Estate gives brokers and developers a single place to list properties, manage leads, and schedule site visits, from enquiry to closed deal.',
            'active' => true,
        ]);

        $this->seedDefaultPolicies($realEstate);

        $realEstate->documentFields()->createMany([
            ['label' => 'Company Logo', 'field_key' => 'company_logo', 'field_type' => 'image', 'placeholder' => 'Upload company logo (PNG/JPG)', 'required' => true],
            ['label' => 'RERA Registration Number', 'field_key' => 'rera_registration_number', 'field_type' => 'text', 'placeholder' => 'e.g. PR/GJ/RAJKOT/1234/2026', 'required' => true],
            ['label' => 'Company PAN', 'field_key' => 'company_pan', 'field_type' => 'text', 'placeholder' => 'e.g. ABCDE1234F', 'required' => true],
            ['label' => 'Office Address Proof', 'field_key' => 'office_address_proof', 'field_type' => 'pdf', 'placeholder' => 'Upload office address proof', 'required' => true],
            ['label' => 'Bank Account Details', 'field_key' => 'bank_account_details', 'field_type' => 'key', 'placeholder' => 'Account number, IFSC, bank name', 'required' => false],
        ]);

        $realEstate->training()->createMany([
            ['title' => 'Getting Started — Login & Dashboard', 'description' => 'Learn how to login and navigate the dashboard.', 'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'duration' => '7:00'],
            ['title' => 'Property Listing Management', 'description' => 'Add and manage property listings with photos and pricing.', 'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'duration' => '10:20'],
            ['title' => 'Lead & Client CRM', 'description' => 'Track enquiries and follow-ups through the sales pipeline.', 'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'duration' => '8:40'],
            ['title' => 'Site Visit Scheduling', 'description' => 'Schedule and track client site visits.', 'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'duration' => '5:15'],
        ]);

        $realEstate->faqs()->createMany([
            ['question' => 'How do I add a new property listing?', 'answer' => 'Go to Properties > Add Property, fill in the details, upload photos, and publish it to your listing page.'],
            ['question' => 'Can I track which leads are most interested?', 'answer' => 'Yes, the CRM pipeline lets you tag lead status (New, Follow-up, Site Visit, Closed) so your team knows priority.'],
        ]);

        $rapidRetail = Product::create([
            'name' => 'Rapid Retail',
            'tagline' => 'Multi-Store Retail Management System',
            'category' => 'Retail',
            'introduction' => 'Rapid Retail is a billing and inventory system built for multi-store retail chains, keeping stock, staff, and sales in sync across every outlet.',
            'active' => true,
        ]);

        $this->seedDefaultPolicies($rapidRetail);

        $rapidRetail->documentFields()->createMany([
            ['label' => 'Store Logo', 'field_key' => 'store_logo', 'field_type' => 'image', 'placeholder' => 'Upload store logo (PNG/JPG)', 'required' => true],
            ['label' => 'GST Number', 'field_key' => 'gst_number', 'field_type' => 'text', 'placeholder' => 'e.g. 24ABCDE1234F1Z5', 'required' => true],
            ['label' => 'Trade License', 'field_key' => 'trade_license', 'field_type' => 'pdf', 'placeholder' => 'Upload trade license', 'required' => true],
            ['label' => 'POS Terminal Key', 'field_key' => 'pos_terminal_key', 'field_type' => 'key', 'placeholder' => 'Terminal ID and activation key', 'required' => true],
            ['label' => 'Payment Gateway Key', 'field_key' => 'payment_gateway_key', 'field_type' => 'key', 'placeholder' => 'rzp_live_...', 'required' => false],
        ]);

        $rapidRetail->training()->createMany([
            ['title' => 'Getting Started — Login & Dashboard', 'description' => 'Learn how to login and navigate the dashboard.', 'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'duration' => '6:50'],
            ['title' => 'Inventory Management', 'description' => 'Add products, manage stock levels, and set reorder alerts.', 'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'duration' => '9:40'],
            ['title' => 'Billing & Checkout', 'description' => 'Process sales, discounts, and returns at the counter.', 'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'duration' => '7:25'],
            ['title' => 'Multi-Store Reports', 'description' => 'Compare sales and stock performance across every store.', 'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'duration' => '8:05'],
        ]);

        $rapidRetail->faqs()->createMany([
            ['question' => 'Can I manage stock across multiple stores?', 'answer' => 'Yes, go to Settings > Stores to add every outlet, and inventory is tracked separately per store.'],
            ['question' => 'What happens when stock runs low?', 'answer' => 'Rapid Retail sends a reorder alert once stock for an item falls below the threshold you set for it.'],
        ]);

        // Clients & Projects are seeded separately in ProjectSeeder, using
        // ClientService/ProjectService so they get real logins, document
        // templates, training progress, timelines and renewals.
    }

    private function seedDefaultPolicies(Product $product): void
    {
        $privacyPolicy = <<<'MD'
This Privacy Policy describes how {name} collects, uses, and shares information about you when you use our services.

**Information We Collect**
We collect information you provide directly to us, such as when you create an account, submit content, or contact us for support.

**How We Use Information**
We use the information we collect to provide, maintain, and improve our services, process transactions, and send you technical notices and support messages.
MD;

        foreach (ProductPolicy::TYPES as $type => $title) {
            $product->policies()->create([
                'type' => $type,
                'title' => $title,
                'content' => $type === 'privacy_policy'
                    ? str_replace('{name}', $product->name, $privacyPolicy)
                    : null,
            ]);
        }
    }
}
