<?php

namespace App\Models;

use App\Models\Application\Status;
use App\Models\Contract\Template;
use DomainException;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use LogicException;

class Contract extends Model
{
    use HasFactory;
    use HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'application_id',
        'contract_template_id',
        'contract_text',
        'signed_by_customer',
    ];

    protected $casts = [
        'signed_by_customer' => 'boolean',
    ];

    public function getRouteKeyName(): string
    {
        return 'id';
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'application_id', 'id');
    }

    public function contractTemplate(): BelongsTo
    {
        return $this->belongsTo(Template::class, 'contract_template_id', 'id');
    }

    public function signByCustomer(): self
    {
        DB::transaction(function () {
            $this->update([
                'signed_by_customer' => true,
            ]);

            $this->application->confirmByCustomer();
        });

        return $this->refresh();
    }
}
