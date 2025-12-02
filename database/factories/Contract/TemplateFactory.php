<?php

namespace Database\Factories\Contract;

use App\Models\Contract\Template;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Template>
 */
class TemplateFactory extends Factory
{
    protected $model = Template::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->optional(0.8)->words(3, true) . ' Template',
            'template_text' => $this->generateTemplateText(),
        ];
    }

    /**
     * Generate a sample contract template text with placeholders.
     */
    private function generateTemplateText(): string
    {
        return <<<'TEMPLATE'
قرارداد فروش اقساطی

این قرارداد بین {{vendor_name}} به عنوان فروشنده و {{customer_name}} به عنوان خریدار منعقد می‌گردد.

مبلغ کل قرارداد: {{total_payable_amount}} ریال
تعداد اقساط: {{number_of_installments}} قسط
نرخ بهره: {{interest_rate}} درصد

طرفین متعهد به اجرای مفاد این قرارداد می‌باشند.
TEMPLATE;
    }
}

