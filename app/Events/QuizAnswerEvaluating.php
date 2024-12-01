<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\GraderInterface;
use App\Models\QuizAnswer;
use Illuminate\Queue\SerializesModels;

final readonly class QuizAnswerEvaluating
{
    use SerializesModels;

    public function __construct(public QuizAnswer $quizAnswer, public int $score, public GraderInterface $grader) {}
}
