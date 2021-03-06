<?php

namespace Rubix\ML\Regressors;

use Rubix\ML\Datasets\Labeled;
use Rubix\ML\Graph\Nodes\Decision;

/**
 * Extra Tree Regressor
 *
 * An *Extremely Randomized* Regression Tree, these trees differ from standard
 * Regression Trees in that they choose a split drawn from a set random set
 * determined by max features, rather than searching the entire column.
 *
 * > **Note**: Decision tree based algorithms can handle both categorical and
 * continuous features at the same time.
 *
 * References:
 * [1] P. Geurts et al. (2005). Extremely Randomized Trees.
 *
 * @category    Machine Learning
 * @package     Rubix/ML
 * @author      Andrew DalPino
 */
class ExtraTreeRegressor extends RegressionTree
{
    /**
     * Randomized algorithm that chooses the split point with the lowest
     * variance among a random assortment of features.
     *
     * @param \Rubix\ML\Datasets\Labeled $dataset
     * @return \Rubix\ML\Graph\Nodes\Decision
     */
    protected function split(Labeled $dataset) : Decision
    {
        $bestImpurity = INF;
        $bestColumn = $bestValue = null;
        $bestGroups = [];

        $max = $dataset->numRows() - 1;

        shuffle($this->columns);

        foreach (array_slice($this->columns, 0, $this->maxFeatures) as $column) {
            $sample = $dataset->row(rand(0, $max));

            $value = $sample[$column];

            $groups = $dataset->partition($column, $value);

            $impurity = $this->splitImpurity($groups);

            if ($impurity < $bestImpurity) {
                $bestColumn = $column;
                $bestValue = $value;
                $bestGroups = $groups;
                $bestImpurity = $impurity;
            }

            if ($impurity < $this->tolerance) {
                break 1;
            }
        }

        return new Decision($bestColumn, $bestValue, $bestGroups, $bestImpurity);
    }
}
