<?php

namespace Rubix\ML\CrossValidation\Metrics;

use Rubix\ML\Estimator;
use InvalidArgumentException;

/**
 * Informedness
 *
 * Informedness is a measure of the probability that an estimator will make an
 * informed decision. The index was suggested by W.J. Youden as a way of
 * summarizing the performance of a diagnostic test. Its value ranges from 0
 * through 1 and has a zero value when the test gives the same proportion of
 * positive results for groups with and without the disease, i.e the test is
 * useless.
 *
 * References:
 * [1] W. J. Youden. (1950). Index for Rating Diagnostic Tests.
 *
 * @category    Machine Learning
 * @package     Rubix/ML
 * @author      Andrew DalPino
 */
class Informedness implements Metric
{
    /**
     * Return a tuple of the min and max output value for this metric.
     *
     * @return float[]
     */
    public function range() : array
    {
        return [0., 1.];
    }

    /**
     * The estimator types that this metric is compatible with.
     *
     * @return int[]
     */
    public function compatibility() : array
    {
        return [
            Estimator::CLASSIFIER,
            Estimator::ANOMALY_DETECTOR,
        ];
    }

    /**
     * Score a set of predictions.
     *
     * @param array $predictions
     * @param array $labels
     * @throws \InvalidArgumentException
     * @return float
     */
    public function score(array $predictions, array $labels) : float
    {
        if (empty($predictions)) {
            return 0.;
        }

        if (count($predictions) !== count($labels)) {
            throw new InvalidArgumentException('The number of labels'
                . ' must equal the number of predictions.');
        }

        $classes = array_unique(array_merge($predictions, $labels));

        $truePositives = $trueNegatives = $falsePositives = $falseNegatives
            = array_fill_keys($classes, 0);

        foreach ($predictions as $i => $prediction) {
            $label = $labels[$i];

            if ($prediction === $label) {
                $truePositives[$prediction]++;

                foreach ($classes as $class) {
                    if ($class !== $prediction) {
                        $trueNegatives[$class]++;
                    }
                }
            } else {
                $falsePositives[$prediction]++;
                $falseNegatives[$label]++;
            }
        }

        return array_sum(array_map(
            [$this, 'compute'],
            $truePositives,
            $trueNegatives,
            $falsePositives,
            $falseNegatives
        ))
            / count($classes);
    }

    /**
     * Compute the class informedness score.
     *
     * @param int $tp
     * @param int $tn
     * @param int $fp
     * @param int $fn
     * @return float
     */
    public function compute(int $tp, int $tn, int $fp, int $fn) : float
    {
        return $tp / (($tp + $fn) ?: self::EPSILON)
            + $tn / (($tn + $fp) ?: self::EPSILON) - 1.;
    }
}
