<?php
declare(strict_types=1);

namespace App\Controller\Api;

use Cake\Event\EventInterface;
use Cake\Http\Response;

/**
 * Articles Controller
 *
 * @property \App\Model\Table\ArticlesTable $entity
 * @method \App\Model\Entity\Article[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ArticlesController extends BaseApiController
{
    /**
     * @param EventInterface $event
     * @return Response|void|null
     */
    public function beforeFilter(EventInterface $event)
    {
        $this->Authentication->allowUnauthenticated(['index', 'view']);
        parent::beforeFilter($event);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response
     */
    public function index(): Response
    {
        $this->Authorization->skipAuthorization();
        $entities = $this->paginate($this->Articles->getWithLikes());
        return $this->renderSuccess($entities->toArray());
    }

    /**
     * View method
     *
     * @param string|null $id Article id.
     * @return \Cake\Http\Response
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null): Response
    {
        $this->Authorization->skipAuthorization();
        $entity = $this->Articles->findWithLikes($id);
        return $this->renderSuccess($entity->toArray());
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response
     */
    public function add(): Response
    {
        $this->Authorization->skipAuthorization();
        $entity = $this->Articles->newEmptyEntity();
        $entity->user_id = $this->Authentication->getIdentity()->id;
        $entity = $this->Articles->patchEntity($entity, $this->request->getData(), [
            // Disable modification of user_id.
            'accessibleFields' => ['user_id' => false]
        ]);
        if ($entity->hasErrors()) {
            return $this->renderBadRequest($entity->getErrors());
        }
        $this->Articles->saveOrFail($entity);
        return $this->renderSuccess($entity->toArray(), __('The article has been saved.'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Article id.
     * @return \Cake\Http\Response
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null): Response
    {
        $entity = $this->Articles->get($id);
        $this->Authorization->authorize($entity);
        $entity = $this->Articles->patchEntity($entity, $this->request->getData(), [
            // Disable modification of user_id.
            'accessibleFields' => ['user_id' => false]
        ]);
        if ($entity->hasErrors()) {
            return $this->renderBadRequest($entity->getErrors());
        }
        $this->Articles->saveOrFail($entity);
        return $this->renderSuccess($entity->toArray(), __('The article has been saved.'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Article id.
     * @return \Cake\Http\Response
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null): Response
    {
        $entity = $this->Articles->get($id);
        $this->Authorization->authorize($entity);
        $this->Articles->delete($entity);
        return $this->renderSuccess([], __('The article has been deleted.'));
    }

    /**
     * Like method
     *
     * @param string|null $id Article id.
     * @return \Cake\Http\Response
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function like($id = null): Response
    {
        $entity = $this->Articles->get($id);
        $this->Authorization->skipAuthorization();

        $like = $this->Articles->Likes->newEmptyEntity();
        $like = $this->Articles->Likes->patchEntity($like, [
            'user_id' => $this->Authentication->getIdentity()->id,
            'article_id' => $entity->id,
        ]);
        if ($like->hasErrors()) {
            return $this->renderBadRequest($like->getErrors());
        }
        $this->Articles->Likes->saveOrFail($like);
        return $this->renderSuccess([], __('Article liked successfully.'));
    }
}
