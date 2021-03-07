<?php

namespace TC\MTI\InlineMod\Report;

use XF\Http\Request;
use XF\InlineMod\AbstractAction;
use XF\Mvc\Entity\AbstractCollection;

class Assign extends AbstractAction
{
    public function getTitle()
    {
        return \XF::phrase('tc_mbi_assign_to');
    }

    protected function canApplyToEntity(\XF\Mvc\Entity\Entity $entity, array $options, &$error = null)
    {
        return $entity->canTcInlineModReport();
    }

    /**
     * @param \XF\Mvc\Entity\Entity $entity
     * @param array $options
     * @throws \XF\PrintableException
     */
    protected function applyToEntity(\XF\Mvc\Entity\Entity $entity, array $options)
    {
        if (!$options['user'])
        {
            throw new \InvalidArgumentException("Please enter valid user");
        }

        /** @var \XF\Repository\User $userRepo */
        $userRepo = \XF::repository('XF:User');
        $recipient = $userRepo->getUserByNameOrEmail($options['user']);

        if (!$recipient->is_moderator)
        {
            throw new \InvalidArgumentException("Recipient is not moderator");
        }

        $entity->assigned_user_id = $recipient->user_id;
        $entity->save();
        $this->returnUrl = $this->app()->router()->buildLink('reports', $entity);
    }

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
            'user' => $request->filter('user', 'str')
        ];

        return $options;
    }
}