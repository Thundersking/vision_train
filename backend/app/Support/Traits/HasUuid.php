<?php

namespace App\Support\Traits;

use Illuminate\Support\Str;

trait HasUuid
{
    protected static function bootHasUuid(): void
    {
        static::creating(function ($model) {
            $uuidField = $model->getUuidFieldName();
            
            if (empty($model->{$uuidField})) {
                $model->{$uuidField} = (string) Str::uuid();
            }
        });
    }
    
    /**
     * Имя поля для UUID
     */
    protected function getUuidFieldName(): string
    {
        return 'uuid';
    }
}
