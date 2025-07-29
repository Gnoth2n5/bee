<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModerationRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'keywords',
        'action',
        'description',
        'is_active',
        'priority',
        'fields_to_check',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'priority' => 'integer',
        'fields_to_check' => 'array',
    ];

    /**
     * Get the user who created this rule.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get keywords as array.
     */
    public function getKeywordsArrayAttribute()
    {
        return array_map('trim', explode(',', $this->keywords));
    }

    /**
     * Scope a query to only include active rules.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to order by priority.
     */
    public function scopeByPriority($query)
    {
        return $query->orderBy('priority', 'desc');
    }

    /**
     * Check if content violates this rule.
     */
    public function checkViolation($content)
    {
        if (!$this->is_active) {
            return false;
        }

        $keywords = $this->getKeywordsArrayAttribute();
        $content = mb_strtolower($content, 'UTF-8');

        foreach ($keywords as $keyword) {
            $keyword = mb_strtolower(trim($keyword), 'UTF-8');
            if (!empty($keyword) && str_contains($content, $keyword)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if recipe violates this rule.
     */
    public function checkRecipeViolation(Recipe $recipe)
    {
        if (!$this->is_active) {
            return false;
        }

        $fieldsToCheck = $this->fields_to_check ?? ['title', 'description', 'summary', 'ingredients', 'instructions', 'tips', 'notes'];

        foreach ($fieldsToCheck as $field) {
            $content = $this->getRecipeFieldContent($recipe, $field);
            if ($content && $this->checkViolation($content)) {
                return [
                    'violated' => true,
                    'field' => $field,
                    'content' => $content,
                    'rule' => $this
                ];
            }
        }

        return false;
    }

    /**
     * Get content from recipe field.
     */
    protected function getRecipeFieldContent(Recipe $recipe, $field)
    {
        switch ($field) {
            case 'title':
                return $recipe->title;
            case 'description':
                return $recipe->description;
            case 'summary':
                return $recipe->summary;
            case 'ingredients':
                return is_array($recipe->ingredients) ? json_encode($recipe->ingredients) : $recipe->ingredients;
            case 'instructions':
                return is_array($recipe->instructions) ? json_encode($recipe->instructions) : $recipe->instructions;
            case 'tips':
                return $recipe->tips;
            case 'notes':
                return $recipe->notes;
            default:
                return null;
        }
    }
}