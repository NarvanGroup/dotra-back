<?php

declare(strict_types=1);

namespace App\Models\Contract;

use App\Models\Contract as ContractModel;
use Database\Factories\Contract\TemplateFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Template extends Model
{
    use HasFactory;
    use HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $table = 'contract_templates';

    protected $fillable = [
        'name',
        'template_text',
    ];

    /**
     * Fill template attributes with provided parameters.
     *
     * @param array<string, mixed> $attributes
     * @return string
     */
    public function fillAttributes(array $attributes): string
    {
        $text = $this->template_text;

        foreach ($attributes as $key => $value) {
            $placeholder = '{{' . $key . '}}';
            $text = str_replace($placeholder, (string) $value, $text);
        }

        return $text;
    }

    /**
     * Get contracts that use this template.
     */
    public function contracts(): HasMany
    {
        return $this->hasMany(ContractModel::class, 'contract_template_id', 'id');
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return TemplateFactory::new();
    }
}

