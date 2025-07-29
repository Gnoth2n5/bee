<?php

namespace App\Services;

use App\Models\ModerationRule;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class ModerationService
{
    /**
     * Auto moderate pending recipes.
     */
    public function autoModeratePendingRecipes()
    {
        $pendingRecipes = Recipe::where('status', 'pending')->get();
        $results = [
            'total' => $pendingRecipes->count(),
            'approved' => 0,
            'rejected' => 0,
            'flagged' => 0,
            'auto_approved' => 0,
            'errors' => []
        ];

        foreach ($pendingRecipes as $recipe) {
            try {
                // Kiểm tra auto approve theo thời gian
                if ($recipe->auto_approve_at && $recipe->auto_approve_at->isPast()) {
                    $this->approveRecipe($recipe, 'Tự động phê duyệt theo lịch trình');
                    $results['auto_approved']++;
                    continue;
                }

                $result = $this->moderateRecipe($recipe);

                switch ($result['action']) {
                    case 'approved':
                        $results['approved']++;
                        break;
                    case 'rejected':
                        $results['rejected']++;
                        break;
                    case 'flagged':
                        $results['flagged']++;
                        break;
                }
            } catch (\Exception $e) {
                $results['errors'][] = [
                    'recipe_id' => $recipe->id,
                    'error' => $e->getMessage()
                ];
                Log::error('Auto moderation error for recipe ' . $recipe->id, [
                    'error' => $e->getMessage(),
                    'recipe' => $recipe->toArray()
                ]);
            }
        }

        Log::info('Auto moderation completed', $results);
        return $results;
    }

    /**
     * Moderate a single recipe.
     */
    public function moderateRecipe(Recipe $recipe)
    {
        $activeRules = ModerationRule::active()->byPriority()->get();
        $violations = [];
        $highestPriorityAction = 'auto_approve';

        foreach ($activeRules as $rule) {
            $violation = $rule->checkRecipeViolation($recipe);

            if ($violation) {
                $violations[] = [
                    'rule' => $rule,
                    'field' => $violation['field'],
                    'content' => $violation['content']
                ];

                // Determine action based on priority
                if ($rule->priority > 0) {
                    $highestPriorityAction = $rule->action;
                }
            }
        }

        // Take action based on violations
        if (empty($violations)) {
            return $this->approveRecipe($recipe, 'Auto-approved by system');
        }

        switch ($highestPriorityAction) {
            case 'reject':
                return $this->rejectRecipe($recipe, $violations);
            case 'flag':
                return $this->flagRecipe($recipe, $violations);
            case 'auto_approve':
            default:
                return $this->approveRecipe($recipe, 'Auto-approved despite violations');
        }
    }

    /**
     * Approve a recipe.
     */
    protected function approveRecipe(Recipe $recipe, string $reason)
    {
        $recipe->update([
            'status' => 'approved',
            'approved_by' => null, // System approval
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);

        Log::info('Recipe auto-approved', [
            'recipe_id' => $recipe->id,
            'title' => $recipe->title,
            'reason' => $reason
        ]);

        return [
            'action' => 'approved',
            'recipe_id' => $recipe->id,
            'reason' => $reason
        ];
    }

    /**
     * Reject a recipe.
     */
    protected function rejectRecipe(Recipe $recipe, array $violations)
    {
        $rejectionReason = $this->buildRejectionReason($violations);

        $recipe->update([
            'status' => 'rejected',
            'approved_by' => null, // System rejection
            'approved_at' => now(),
            'rejection_reason' => $rejectionReason,
        ]);

        Log::info('Recipe auto-rejected', [
            'recipe_id' => $recipe->id,
            'title' => $recipe->title,
            'violations' => $violations,
            'reason' => $rejectionReason
        ]);

        return [
            'action' => 'rejected',
            'recipe_id' => $recipe->id,
            'reason' => $rejectionReason,
            'violations' => $violations
        ];
    }

    /**
     * Flag a recipe for manual review.
     */
    protected function flagRecipe(Recipe $recipe, array $violations)
    {
        // Keep status as pending but add a note
        $flagNote = $this->buildRejectionReason($violations);

        // You might want to add a new field to store flag notes
        // For now, we'll use rejection_reason field
        $recipe->update([
            'rejection_reason' => 'FLAGGED: ' . $flagNote,
        ]);

        Log::info('Recipe flagged for review', [
            'recipe_id' => $recipe->id,
            'title' => $recipe->title,
            'violations' => $violations,
            'note' => $flagNote
        ]);

        return [
            'action' => 'flagged',
            'recipe_id' => $recipe->id,
            'note' => $flagNote,
            'violations' => $violations
        ];
    }

    /**
     * Build rejection reason from violations.
     */
    protected function buildRejectionReason(array $violations)
    {
        $reasons = [];

        foreach ($violations as $violation) {
            $rule = $violation['rule'];
            $field = $violation['field'];

            $reasons[] = sprintf(
                'Vi phạm quy tắc "%s" trong trường "%s"',
                $rule->name,
                $this->getFieldDisplayName($field)
            );
        }

        return 'Tự động từ chối: ' . implode('; ', $reasons);
    }

    /**
     * Get display name for field.
     */
    protected function getFieldDisplayName($field)
    {
        $fieldNames = [
            'title' => 'Tiêu đề',
            'description' => 'Mô tả',
            'summary' => 'Tóm tắt',
            'ingredients' => 'Nguyên liệu',
            'instructions' => 'Hướng dẫn',
            'tips' => 'Mẹo',
            'notes' => 'Ghi chú'
        ];

        return $fieldNames[$field] ?? $field;
    }

    /**
     * Test a recipe against all rules (for preview).
     */
    public function testRecipeModeration(Recipe $recipe)
    {
        $activeRules = ModerationRule::active()->byPriority()->get();
        $results = [];

        foreach ($activeRules as $rule) {
            $violation = $rule->checkRecipeViolation($recipe);

            $results[] = [
                'rule' => $rule,
                'violated' => $violation !== false,
                'violation_details' => $violation
            ];
        }

        return $results;
    }
}