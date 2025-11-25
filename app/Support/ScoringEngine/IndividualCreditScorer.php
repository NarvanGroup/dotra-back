<?php

namespace App\Support\ScoringEngine;

class IndividualCreditScorer
{
    /**
     * Calculate credit score for an individual person.
     *
     * @param string $nationalCode
     * @param string $mobile
     * @return array{
     *     final_score: int,
     * }
     */
    public function calculate(string $nationalCode, string $mobile): array
    {
        return [
            'final_score' => 100 * ((int) $nationalCode % 9 + 1 ),
        ];
    }
}

