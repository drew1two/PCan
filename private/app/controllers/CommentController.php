<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Pcan\Controllers;
use Phalcon\Tag;
use Phalcon\Mvc\View;
use Pcan\Models\BlogComment; 
use Pcan\Forms\CommentForm;

class CommentController extends \Pcan\Controllers\ControllerBase
{

    public function fetchAction()
    {
        $request =$this->request;
        if ($request->isPost()==true) {
            if ($request->isAjax() == true) {
                    $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
                    $id = $this->request->getPost('id','int');
                    $comment = BlogComment::findFirstByid($id);
                    $this->view->form = new CommentForm($comment, array(
            'edit' => true));        
            }
        }
    }
    /** Comment edited (text and title only)
     * 
     */
    public function updateAction()
    {
        $request = $this->request;
        if ($request->isPost()==true)
        {
            $id = $request->getPost('id','int');
            $comment = BlogComment::findFirstByid($id);
            $updateComment = $request->getPost('comment');
            $updateTitle = $request->getPost('title','striptags');

            if (($updateComment !== $comment->comment)||($updateTitle !== $comment->title))
            {
                $comment->comment = $updateComment;
                $comment->title = $updateTitle;
                if (!$comment->save())
                {
                    $this->flash->error($comment->getMessages());
                }
                else {
                    Tag::resetInput();
                }  
            }
            $this->response->redirect('read/article/' . $comment->blog_id);
        }
        
    }
    public function newAction()
    {
        if (!$this->request->isPost())
        {
            return; // got here by mistake ? 
        }
        $comment = new BlogComment();
        $comment->assign(array(
         'blog_id' => $this->request->getPost('blog_id', 'int'),
         'user_id' => $this->request->getPost('user_id', 'int'),
         'title' => $this->request->getPost('title','striptags'),
         'date_comment' => date('Y-m-d H:i:s'),
         'reply_to_id' => $this->request->getPost('reply_to_id','int'),
         'head_id' => $this->request->getPost('head_id','int'),
         'enabled' => 1,
         'comment' => $this->request->getPost('comment'),
         ));
        //manual filter for comment
        //$comment->comment = $this->request->getPost('comment');

        if ($comment->head_id==0)
            $comment->head_id = null;
        if ($comment->reply_to_id==0)
            $comment->reply_to_id = null;
        if (!is_null($comment->reply_to_id))
        {          
           // get the head comment id from previous comment.
            $repliedComment = BlogComment::findFirst($comment->reply_to_id);
            if (is_null($repliedComment->head_id) || $repliedComment->head_id==0)
            {
                $comment->head_id = $comment->reply_to_id;
            }
            else {
                $comment->head_id = $repliedComment->head_id;
            }
        }
        if (!$comment->save()) {
            $this->flash->error($comment->getMessages());
            Tag::resetInput();
                
        } else {

                $this->flash->success("Comment was added successfully " . $comment->comment);

                Tag::resetInput();
        }
    }

}