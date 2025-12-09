<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    protected $guarded = [];

    protected function asBoolean($key, $value)
    {
        return (bool) $value;
    }

    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);

        if (in_array($key, $this->getBooleanColumns())) {
            return (bool) $value;
        }

        return $value;
    }

    protected function getBooleanColumns()
    {
        return property_exists($this, 'booleans') ? $this->booleans : [];
    }
}
