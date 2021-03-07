<?php

namespace TC\MTI\InlineMod\Report;

use XF\Http\Request;
use XF\InlineMod\AbstractAction;
use XF\Mvc\Entity\AbstractCollection;

class Assign extends AbstractAction
{
    public function getTitle() : \XF\Phrase
    {
        return \XF::phrase('tc_mti_assign_to');
    }

    /**
     * @param \XF\Mvc\Entity\Entity $entity
     * @param array $options
     * @param null $error
     * @return mixed
     */
    protected function canApplyToEntity(\XF\Mvc\Entity\Entity $entity, array $options, &$error = null)
    {
        return \XF::visitor()->canTcInlineModReport();
    }

    /**
     * @param \XF\Mvc\Entity\Entity $entity
     * @param array $options
     * @throws \XF\PrintableException
     */
    protected function applyToEntity(\XF\Mvc\Entity\Entity $entity, array $options)
    {
        /** @var \XF\Repository\User $userRepo */
        $userRepo = \XF::repository('XF:User');
        $user = $userRepo->getUserByNameOrEmail($options['user']);

        $entity->assigned_user_id = $user->user_id;
        $entity->save();

        /** @var \XF\Service\Report\Commenter $commenter */
        $commenter = \XF::service('XF:Report\Commenter', $entity);
        $commenter->setMessage(\XF::phrase('tc_mti_report_assigned_to_x', ['username' => $user->username]));
        $commenter->save();

        if ($options['alert'])
        {
            $closureNotifier = \XF::service('XF:Report\ClosureNotifier', $entity);
            $closureNotifier->setAlertType('assign');
            $closureNotifier->setAlertComment($options['alert_message']);
            $closureNotifier->notify();
        }

        $this->returnUrl = $this->app()->router()->buildLink('reports', $entity);
    }

    /**
     * @param AbstractCollection $entities
     * @param \XF\Mvc\Controller $controller
     * @return \XF\Mvc\Reply\View|null
     */
    public function renderForm(AbstractCollection $entities, \XF\Mvc\Controller $controller)
    {
        $viewParams = [
            'reports' => \XF::finder('XF:Report')->fetch(),
            'total' => count($entities)
        ];

        return $controller->view('XF:Report:Public:InlineMod\Assign', 'tc_mti_inline_mod_assign', $viewParams);
    }

    /**
     * @param AbstractCollection $entities
     * @param Request $request
     * @return array
     */
    public function getFormOptions(AbstractCollection $entities, Request $request)
    {
        $options = [
            'user' => $request->filter('user', 'str'),
            'alert' => $request->filter('alert', 'bool'),
            'alert_message' => $request->filter('alert_message', 'str')
        ];

        return $options;
    }
}