<?php

declare(strict_types = 1);

namespace Tests\unit;

use PHPUnit\Framework\TestCase;
use SocialPost\Dto\SocialPostTo;
use Statistics\Calculator\AvgNumberOfPostsPerUserPerMonth;
use Statistics\Dto\ParamsTo;
use Statistics\Enum\StatsEnum;

/**
 * Class ATestTest
 *
 * @package Tests\unit
 */
class TestTest extends TestCase
{
    /**
     * @test
     */
    public function testCalculatesAveragePerMonthPerUser(): void
    {
        // jan: 1 post by 1 author, feb: 3 posts by 2 authors
        // 1 and 1.5
        $postTo1 = (new SocialPostTo())
            ->setDate(date_create('january 1'))
            ->setAuthorId('some-author1');
        $postTo2 = (new SocialPostTo())
            ->setDate(date_create('february 1'))
            ->setAuthorId('some-author1');
        $postTo3 = (new SocialPostTo())
            ->setDate(date_create('february 1'))
            ->setAuthorId('some-author2');
        $postTo4 = (new SocialPostTo())
            ->setDate(date_create('february 1'))
            ->setAuthorId('some-author1');

        $paramsTo = (new ParamsTo())
            ->setStatName(StatsEnum::AVERAGE_POSTS_NUMBER_PER_USER_PER_MONTH)
            ->setStartDate(date_create('january 1'))
            ->setEndDate(date_create('march 1'));

        $calculator = (new AvgNumberOfPostsPerUserPerMonth());
        $calculator->setParameters($paramsTo);
        $calculator->accumulateData($postTo1);
        $calculator->accumulateData($postTo2);
        $calculator->accumulateData($postTo3);
        $calculator->accumulateData($postTo4);

        $stats = $calculator->calculate();

        $this->assertEquals(1, $stats->getChildren()[0]->getValue());
        $this->assertEquals(1.5, $stats->getChildren()[1]->getValue());
    }
}
