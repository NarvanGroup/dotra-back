<?php

namespace Database\Seeders\Contract;

use App\Models\Contract\Template;
use Illuminate\Database\Seeder;

class TemplateSeeder extends Seeder
{
    public function run(): void
    {
        // Create a default contract template if none exists
        if (Template::count() === 0) {
            Template::factory()->create([
                'name' => 'Default Contract Template',
                'template_text' => <<<'TEMPLATE'
قرارداد فروش اقساطی

این قرارداد بین {{vendor_name}} به عنوان فروشنده و {{customer_name}} به عنوان خریدار منعقد می‌گردد.

مبلغ کل قرارداد: {{total_payable_amount}} ریال
تعداد اقساط: {{number_of_installments}} قسط
نرخ بهره: {{interest_rate}} درصد

طرفین متعهد به اجرای مفاد این قرارداد می‌باشند.

تاریخ: ' . now()->format('Y/m/d') . '
TEMPLATE
            ]);
        }

        // Create additional templates
        Template::factory(2)->create();
    }
}

