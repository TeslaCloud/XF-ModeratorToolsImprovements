<?php

namespace TC\MTI\XF\Pub\Controller;

class Post extends XFCP_Post
{
    /**
     * @return \XF\Mvc\Reply\Redirect|\XF\Mvc\Reply\View
     * @throws \XF\PrintableException
     */
    public function actionReactionsDelete()
    {
        $reactionId = $this->filter('reaction', 'int');

        if ($this->isPost())
        {
            $reaction = $this->finder('XF:ReactionContent')
                ->where('reaction_content_id', $this->filter('reaction', 'int'))
                ->fetchOne();

            if (!is_null($reaction))
            {
                $reaction->delete();

                return $this->redirect($this->getDynamicRedirect());
            }
        }

        return $this->view('XF\Post:Reactions\Delete', 'tc_mti_post_reaction_delete', ['reaction' => $reactionId]);
    }
}