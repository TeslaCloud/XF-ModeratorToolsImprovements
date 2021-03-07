<?php

namespace TC\MTI\InlineMod;

use XF\InlineMod\AbstractHandler;
use XF\Mvc\Entity\Entity;

class Report extends AbstractHandler
{
    /**
     * @return array|\XF\InlineMod\AbstractAction[]
     */
    public function getPossibleActions()
    {
        $actions = [];

        $actions['reject'] = $this->getSimpleActionHandler(
            \XF::phrase('reject'),
            'canTcInlineModReport',
            function (Entity $entity)
            {
                /** @var \XF\Entity\Report $entity */
                if ($entity->report_state == 'open')
                {
                    $entity->report_state = 'rejected';
                    $entity->save();
                }
            }
        );

        $actions['resolve'] = $this->getSimpleActionHandler(
            \XF::phrase('tc_mti_report_resolve'),
            'canTcInlineModReport',
            function (Entity $entity)
            {
                /** @var \XF\Entity\Report $entity */
                if ($entity->report_state == 'open')
                {
                    $entity->report_state = 'resolved';
                    $entity->save();
                }
            }
        );

        $actions['unreject'] = $this->getSimpleActionHandler(
            \XF::phrase('tc_mti_report_unreject'),
            'canTcInlineModReport',
            function (Entity $entity)
            {
                /** @var \XF\Entity\Report $entity */
                if ($entity->report_state == 'rejected')
                {
                    $entity->report_state = 'open';
                    $entity->save();
                }
            }
        );

        $actions['unresolve'] = $this->getSimpleActionHandler(
            \XF::phrase('tc_mti_report_unresolve'),
            'canTcInlineModReport',
            function (Entity $entity)
            {
                /** @var \XF\Entity\Report $entity */
                if ($entity->report_state == 'resolved')
                {
                    $entity->report_state = 'open';
                    $entity->save();
                }
            }
        );

        $actions['assign'] = $this->getActionHandler('TC\MTI:Report\Assign');

        return $actions;
    }
}