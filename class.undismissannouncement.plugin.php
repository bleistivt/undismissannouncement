<?php

class UndismissAnnouncementPlugin extends Gdn_Plugin {

    public function discussionController_undismiss_create($sender, $discussionID) {
        if (!Gdn::request()->isAuthenticatedPostBack()) {
            throw permissionException();
        }

        $model = $sender->DiscussionModel;
        if (!$discussion = $model->getID($discussionID)) {
            throw notFoundException('Discussion');
        }

        $where = [
            'DiscussionID' => $discussion->DiscussionID,
            'UserID' => Gdn::session()->UserID
        ];

        $userDiscussion = $model->SQL->getWhere('UserDiscussion', $where)->firstRow();

        if ($userDiscussion) {
            $model->SQL->put('UserDiscussion', ['Dismissed' => 0], $where);
        }

        $discussion->Dismissed = 0;

        $sender->jsonTarget('', '', 'Refresh');

        $sender->render('blank', 'utility', 'dashboard');
    }


    public function base_discussionOptionsDropdown_handler($sender, $args) {
        $discussion = $args['Discussion'];

        if (Gdn::session()->isValid() && $discussion->Announce && $discussion->Dismissed) {
            $args['DiscussionOptionsDropdown']->addLink(
                t('Undismiss'),
                '/discussion/undismiss/'.$discussion->DiscussionID,
                'undismiss',
                'Undismiss Hijack'
            );
        }
    }

}
