<?php

namespace App\Models;
use Gomee\Models\Model;

/**
 * GPTCriteria class
 *
 * @property string $name Name
 * @property string $label Label
 * @property string $type Type
 * @property string $description Description
 * @property string $placeholder placeholder
 */
class GPTCriteria extends Model
{
    public $table = 'gpt_criteria';
    public $fillable = ['name', 'label', 'type', 'description', 'placeholder'];



}
