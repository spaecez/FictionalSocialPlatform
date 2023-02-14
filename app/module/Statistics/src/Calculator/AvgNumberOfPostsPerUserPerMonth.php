<?php

declare(strict_types = 1);

namespace Statistics\Calculator;

use SocialPost\Dto\SocialPostTo;
use Statistics\Dto\StatisticsTo;

class AvgNumberOfPostsPerUserPerMonth extends AbstractCalculator
{

    protected const UNITS = 'posts';

    private array $totals = [];
    private array $uniqueUsersPerKey = [];

    /**
     * @inheritDoc
     */
    protected function doAccumulate(SocialPostTo $postTo): void
    {
        $key = $postTo->getDate()->format('M, Y');

        $this->totals[$key] = ($this->totals[$key] ?? 0) + 1;
        $this->uniqueUsersPerKey[$key][$postTo->getAuthorId()] = true;
    }

    /**
     * @inheritDoc
     */
    protected function doCalculate(): StatisticsTo
    {
        $stats = new StatisticsTo();
        foreach ($this->totals as $splitPeriod => $total) {
            $avgPerUserPerMonth = round(
                $total / count($this->uniqueUsersPerKey[$splitPeriod]),
                2
            );

            $child = (new StatisticsTo())
                ->setName($this->parameters->getStatName())
                ->setSplitPeriod($splitPeriod)
                ->setValue($avgPerUserPerMonth)
                ->setUnits(self::UNITS);

            $stats->addChild($child);
        }

        return $stats;
    }
}
