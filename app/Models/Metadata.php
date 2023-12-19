<?php

namespace App\Models;
use Gomee\Models\Model;

/**
 * Metadata class
 *
 * @property string $id Id
 * @property string $ref Ref
 * @property string $ref_id Ref Id
 * @property string $name Name
 * @property string $value Value
 */
class Metadata extends Model
{
    public $table = 'metadatas';
    public $fillable = ['ref', 'ref_id', 'name', 'value'];

    public $timestamps = false;

}
