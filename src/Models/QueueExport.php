<?php

namespace Elysiumrealms\QueueExporter\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class QueueExport extends Model
{
    use HasDateTimeFormatter;

    public $incrementing = false;
    protected $keyType = 'string';

    const STATUS_PENDING = 0;
    const STATUS_PROCESSING = 1;
    const STATUS_COMPLETED = 2;

    /**
     * Bootstrap the model and its traits.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) Str::uuid()->getHex();
        });

        static::deleting(function ($model) {
            $file = $model->filename;

            if (Storage::disk('exports')->exists($file)) {
                Storage::disk('exports')->delete($file);
            }
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'filename',
        'expires_at',
    ];
}
