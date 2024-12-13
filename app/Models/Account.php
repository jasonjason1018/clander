<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Mockery\Exception;

class Account extends Model
{
    protected $table = 'accounts';
    protected $primaryKey = 'id_account';
    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'note'
    ];

    protected $encrypted = ['password'];

    const ACCOUNT_STATUS = [
        'deactivate' => 0,
        'active' => 1,
    ];

    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);

        if (in_array($key, $this->encrypted)) {
            if ($this->isNotNullOrEmptyString($value)) {
                $value = Crypt::decrypt($value);
            }
        }

        return $value;
    }

    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->encrypted)) {
            $this->attributes[$key] = Crypt::encrypt($value);
        } else {
            parent::setAttribute($key, $value);
        }

        return $this;
    }

    private function isNotNullOrEmptyString($value)
    {
        if (is_null($value)) {
            return false;
        }

        if ($value == '') {
            return false;
        }

        return true;
    }
}
