<?php


class ESphinxQlCriteria extends CDbCriteria
{
    /**
     * OPTION clause.
     * This is a Sphinx specific extension that lets you control a number of per-query options.
     *
     * @var string
     */
    public $option;

    /**
     * WITHIN GROUP ORDER BY clause.
     * This is a Sphinx specific extension that lets you control how the best row within a group will to be selected
     *
     * @var string
     */
    public $withinGroupOrder;
}