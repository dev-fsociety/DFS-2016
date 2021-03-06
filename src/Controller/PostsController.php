<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Posts Controller
 *
 * @property \App\Model\Table\PostsTable $Posts
 */
class PostsController extends AppController
{
    public function isOwnedBy($params, $user)
    {
        return $this->Posts->find()->where(['id' => $params['pass'][0]])->first()->user_id === (int)$user['id'];
    }

    public function isAuthorized($user)
    {
        if(isset($user))
        {
            if($user['role'] === 0)
            {
                if(in_array($this->request->action, ['edit', 'delete']) && $this->isOwnedBy($this->request->params, $user))
                {
                    return true;
                }

                if(in_array($this->request->action, ['add']))
                {
                    return true;
                }
            }
            
            if(in_array($this->request->action, ['view', 'index', 'byCategory']))
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Categories']
        ];

        $posts = $this->paginate($this->Posts);

        $categories = $this->Posts->Categories->find()->all();

        $this->set(compact('posts', 'categories'));
        $this->set('_serialize', ['posts']);
    }

    /**
     * View method
     *
     * @param string|null $id Post id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $post = $this->Posts->get($id, [
            'contain' => ['Categories']
        ]);

        $this->set('post', $post);
        $this->set('_serialize', ['post']);
    }

    public function byCategory($category_id)
    {
        $posts = $this->Posts->find()->where(['category_id' => $category_id]);
        $category = $this->Posts->Categories->get($category_id);

        $this->set('posts', $posts);
        $this->set('category', $category);
        $this->set('_serialize', ['posts']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $post = $this->Posts->newEntity();
        if ($this->request->is('post')) {
            $post = $this->Posts->patchEntity($post, $this->request->data);
            if ($this->Posts->save($post)) {
                $this->Flash->success(__('The post has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The post could not be saved. Please, try again.'));
            }
        }
        $categories = $this->Posts->Categories->find('list', ['limit' => 200]);
        $this->set(compact('post', 'categories'));
        $this->set('_serialize', ['post']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Post id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $post = $this->Posts->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $post = $this->Posts->patchEntity($post, $this->request->data);
            if ($this->Posts->save($post)) {
                $this->Flash->success(__('The post has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The post could not be saved. Please, try again.'));
            }
        }
        $categories = $this->Posts->Categories->find('list', ['limit' => 200]);
        $this->set(compact('post', 'categories'));
        $this->set('_serialize', ['post']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Post id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $post = $this->Posts->get($id);
        if ($this->Posts->delete($post)) {
            $this->Flash->success(__('The post has been deleted.'));
        } else {
            $this->Flash->error(__('The post could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
