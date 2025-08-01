<?php

namespace App\Services;

use App\Models\ModerationRule;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\RecipeRejected;

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

                // Kiểm tra auto reject theo thời gian
                if ($recipe->auto_reject_at && $recipe->auto_reject_at->isPast()) {
                    $this->rejectRecipe($recipe, [
                        [
                            'rule' => null,
                            'field' => 'scheduled_rejection',
                            'content' => 'Tự động từ chối theo lịch trình'
                        ]
                    ]);
                    $results['rejected']++;
                    continue;
                }

                $result = $this->moderateRecipe($recipe);

                switch ($result['action']) {
                    case 'approved':
                        $results['approved']++;
                        break;
                    case 'rejected':
                        $results['rejected']++;
                        // Gửi notification cho user
                        $this->notifyUserOfRejection($recipe, $result['reason']);
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
     * Moderate a single recipe with improved logic.
     */
    public function moderateRecipe(Recipe $recipe)
    {
        // Kiểm tra các tiêu chí cơ bản trước
        $basicCheck = $this->performBasicChecks($recipe);
        if ($basicCheck['should_reject']) {
            return $this->rejectRecipe($recipe, $basicCheck['violations']);
        }

        // Kiểm tra các quy tắc moderation
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

        // Kiểm tra điểm số tổng thể
        $score = $this->calculateRecipeScore($recipe);

        // Nếu có vi phạm nghiêm trọng, từ chối ngay
        if (!empty($violations) && $highestPriorityAction === 'reject') {
            return $this->rejectRecipe($recipe, $violations);
        }

        // Nếu điểm số thấp và có vi phạm, từ chối
        if ($score < 60 && !empty($violations)) {
            $violations[] = [
                'rule' => null,
                'field' => 'overall_quality',
                'content' => 'Điểm chất lượng tổng thể thấp: ' . $score . '/100'
            ];
            return $this->rejectRecipe($recipe, $violations);
        }

        // Nếu có vi phạm nhẹ, flag để review thủ công
        if (!empty($violations) && $highestPriorityAction === 'flag') {
            return $this->flagRecipe($recipe, $violations);
        }

        // Nếu không có vi phạm hoặc vi phạm nhẹ, phê duyệt
        if (empty($violations) || $score >= 70) {
            return $this->approveRecipe($recipe, 'Auto-approved by system');
        }

        // Trường hợp còn lại, flag để review
        return $this->flagRecipe($recipe, $violations);
    }

    /**
     * Perform basic quality checks.
     */
    protected function performBasicChecks(Recipe $recipe)
    {
        $violations = [];
        $shouldReject = false;

        // Kiểm tra độ dài tối thiểu
        if (strlen($recipe->title) < 5) {
            $violations[] = [
                'rule' => null,
                'field' => 'title',
                'content' => 'Tiêu đề quá ngắn (tối thiểu 5 ký tự)'
            ];
            $shouldReject = true;
        }

        if (strlen($recipe->description) < 20) {
            $violations[] = [
                'rule' => null,
                'field' => 'description',
                'content' => 'Mô tả quá ngắn (tối thiểu 20 ký tự)'
            ];
            $shouldReject = true;
        }

        // Kiểm tra nguyên liệu
        if (empty($recipe->ingredients) || (is_array($recipe->ingredients) && count($recipe->ingredients) < 2)) {
            $violations[] = [
                'rule' => null,
                'field' => 'ingredients',
                'content' => 'Cần ít nhất 2 nguyên liệu'
            ];
            $shouldReject = true;
        }

        // Kiểm tra hướng dẫn
        if (empty($recipe->instructions) || (is_array($recipe->instructions) && count($recipe->instructions) < 2)) {
            $violations[] = [
                'rule' => null,
                'field' => 'instructions',
                'content' => 'Cần ít nhất 2 bước hướng dẫn'
            ];
            $shouldReject = true;
        }

        // Kiểm tra thời gian nấu
        if ($recipe->cooking_time < 5) {
            $violations[] = [
                'rule' => null,
                'field' => 'cooking_time',
                'content' => 'Thời gian nấu không hợp lý (tối thiểu 5 phút)'
            ];
            $shouldReject = true;
        }

        return [
            'should_reject' => $shouldReject,
            'violations' => $violations
        ];
    }

    /**
     * Calculate overall recipe quality score (0-100).
     */
    protected function calculateRecipeScore(Recipe $recipe)
    {
        $score = 0;

        // Tiêu đề (20 điểm)
        $titleLength = strlen($recipe->title);
        if ($titleLength >= 10 && $titleLength <= 100) {
            $score += 20;
        } elseif ($titleLength >= 5) {
            $score += 15;
        }

        // Mô tả (20 điểm)
        $descLength = strlen($recipe->description);
        if ($descLength >= 50 && $descLength <= 500) {
            $score += 20;
        } elseif ($descLength >= 20) {
            $score += 15;
        }

        // Nguyên liệu (20 điểm)
        if (is_array($recipe->ingredients)) {
            $ingredientCount = count($recipe->ingredients);
            if ($ingredientCount >= 5) {
                $score += 20;
            } elseif ($ingredientCount >= 3) {
                $score += 15;
            } elseif ($ingredientCount >= 2) {
                $score += 10;
            }
        }

        // Hướng dẫn (25 điểm)
        if (is_array($recipe->instructions)) {
            $instructionCount = count($recipe->instructions);
            if ($instructionCount >= 5) {
                $score += 25;
            } elseif ($instructionCount >= 3) {
                $score += 20;
            } elseif ($instructionCount >= 2) {
                $score += 15;
            }
        }

        // Thời gian nấu (10 điểm)
        if ($recipe->cooking_time >= 10 && $recipe->cooking_time <= 180) {
            $score += 10;
        } elseif ($recipe->cooking_time >= 5) {
            $score += 5;
        }

        // Hình ảnh (5 điểm)
        if ($recipe->featured_image) {
            $score += 5;
        }

        return min(100, $score);
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
            'published_at' => now(),
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
            if ($violation['rule']) {
                $rule = $violation['rule'];
                $field = $violation['field'];

                $reasons[] = sprintf(
                    'Vi phạm quy tắc "%s" trong trường "%s"',
                    $rule->name,
                    $this->getFieldDisplayName($field)
                );
            } else {
                $reasons[] = $violation['content'];
            }
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
            'notes' => 'Ghi chú',
            'overall_quality' => 'Chất lượng tổng thể'
        ];

        return $fieldNames[$field] ?? $field;
    }

    /**
     * Notify user of recipe rejection.
     */
    protected function notifyUserOfRejection(Recipe $recipe, string $reason)
    {
        try {
            $user = $recipe->user;
            if ($user) {
                Notification::send($user, new RecipeRejected($recipe, $reason));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send rejection notification', [
                'recipe_id' => $recipe->id,
                'user_id' => $recipe->user_id,
                'error' => $e->getMessage()
            ]);
        }
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

        // Thêm kiểm tra cơ bản
        $basicCheck = $this->performBasicChecks($recipe);
        $results[] = [
            'rule' => null,
            'name' => 'Basic Quality Checks',
            'violated' => $basicCheck['should_reject'],
            'violation_details' => $basicCheck['violations']
        ];

        // Thêm điểm số
        $score = $this->calculateRecipeScore($recipe);
        $results[] = [
            'rule' => null,
            'name' => 'Quality Score',
            'violated' => $score < 60,
            'violation_details' => ['score' => $score, 'message' => "Điểm chất lượng: {$score}/100"]
        ];

        return $results;
    }
}