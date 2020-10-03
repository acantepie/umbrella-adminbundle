<?php


namespace Umbrella\AdminBundle\FileWriter;

/**
 * Throwed when limit of task scheduled was reached
 *
 * Class FileWriterMaxScheduleReachedException
 */
class MaxTaskReachedException extends \Exception
{

    /**
     * @var int
     */
    private $maxTask;

    /**
     * FileWriterMaxScheduleReachedException constructor.
     * @param $max
     */
    public function __construct($maxTask)
    {
        $this->maxTask = $maxTask;
        parent::__construct(sprintf('FileWriter max task limit was reached, max = %d.', $maxTask));
    }

    /**
     * @return int
     */
    public function getMaxTask()
    {
        return $this->maxTask;
    }

}