<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller\Api;

use Cake\ORM\TableRegistry;

/**
 * App\Controller\Api\ArticlesController Test Case
 *
 * @uses \App\Controller\Api\ArticlesController
 */
class ArticlesControllerTest extends BaseApiControllerTest
{

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.Articles',
        'app.Users',
        'app.Likes',
    ];

    /**
     * Test index method
     * @group article
     * @group success
     * @return void
     * @uses \App\Controller\ArticlesController::index()
     */
    public function testIndexSuccessWithUnauthorized(): void
    {
        $this->get('/articles.json');

        $this->assertSuccess();
        $this->assertJsonContains('data.0.id', 1);
    }

    /**
     * Test index method
     * @group article
     * @group success
     * @return void
     * @uses \App\Controller\ArticlesController::index()
     */
    public function testIndexSuccessWithAuthorized(): void
    {
        $this->configRequestWithAuthHeader();
        $this->get('/articles.json');

        $this->assertSuccess();
        $this->assertJsonContains('data.0.id', 1);
    }

    /**
     * Test view method
     * @group article
     * @group success
     * @return void
     * @uses \App\Controller\ArticlesController::view()
     */
    public function testViewArticleSuccessWithAuthorized(): void
    {
        $this->configRequestWithAuthHeader();
        $this->get('/articles/1.json');

        $this->assertSuccess();
        $this->assertJsonContains('data.id', 1);
        $this->assertJsonContains('data.total_likes', 0);
    }

    /**
     * Test view method
     * @group article
     * @group success
     * @return void
     * @uses \App\Controller\ArticlesController::view()
     */
    public function testViewArticleSuccessWithUnauthorized(): void
    {
        $this->configRequestWithAuthHeader();
        $this->get('/articles/1.json');

        $this->assertSuccess();
        $this->assertJsonContains('data.id', 1);
        $this->assertJsonContains('data.total_likes', 0);
    }

    /**
     * Test view method
     * @group article
     * @group error
     * @return void
     * @uses \App\Controller\ArticlesController::view()
     */
    public function testViewArticleErrorNotFound(): void
    {
        $this->get('/articles/10.json');
        $this->assertNotFound();
    }

    /**
     * Test edit method
     * @group article
     * @group success
     * @return void
     * @uses \App\Controller\ArticlesController::edit()
     */
    public function testAddArticleSuccess(): void
    {
        $data = [
            'title' => 'Test Article',
            'body' => 'This is a test article.',
            'user_id' => 11,
        ];

        $this->configRequestWithAuthHeader();
        $this->post('/articles.json', $data);
        // response check
        $this->assertSuccess();
        $this->assertJsonContains('data.id', 3);
        $this->assertJsonContains('data.user_id', 1);
        $this->assertJsonContains('data.body', $data['body']);
        $this->assertJsonContains('data.title', $data['title']);
        // db check
        $articlesTable = TableRegistry::getTableLocator()->get('Articles');
        $query = $articlesTable->find()->where(['title' => $data['title'], 'body' => $data['body'], 'user_id' => 1]);
        $this->assertEquals(1, $query->count());
    }

    /**
     * Test edit method
     * @group article
     * @group error
     * @return void
     * @uses \App\Controller\ArticlesController::edit()
     */
    public function testAddArticleErrorUnauthorized(): void
    {
        $data = [
            'title' => 'Test Article',
            'body' => 'This is a test article.',
        ];

        $this->post('/articles.json', $data);
        $this->assertUnauthorized();
        // db check
        $articlesTable = TableRegistry::getTableLocator()->get('Articles');
        $query = $articlesTable->find()->where(['title' => $data['title'], 'body' => $data['body']]);
        $this->assertEquals(0, $query->count());
    }

    /**
     * Test edit method
     * @group article
     * @group error
     * @return void
     * @uses \App\Controller\ArticlesController::edit()
     */
    public function testAddArticleErrorBadRequest(): void
    {
        // title require rule
        $data = [];
        $this->configRequestWithAuthHeader();
        $articlesTable = TableRegistry::getTableLocator()->get('Articles');

        $this->post('/articles.json', $data);
        $this->assertBadRequest();
        $this->assertJsonContains('errors.title', __('This field is required'));
        // db check
        $query = $articlesTable->find();
        $this->assertEquals(2, $query->count());
        // title not empty rule
        $data = [
            'title' => ''
        ];

        $this->configRequestWithAuthHeader();
        $this->post('/articles.json', $data);
        $this->assertBadRequest();
        $this->assertJsonContains('errors.title', __('This field cannot be left empty'));
        // db check
        $query = $articlesTable->find();
        $this->assertEquals(2, $query->count());
        // title max length rule
        $data = [
            'title' => str_repeat('a', 300)
        ];

        $this->configRequestWithAuthHeader();
        $this->post('/articles.json', $data);
        $this->assertBadRequest();
        $this->assertJsonContains('errors.title', __('The provided value is invalid'));

        // db check
        $query = $articlesTable->find()->where(['title' => $data['title']]);
        $this->assertEquals(0, $query->count());
    }

    /**
     * Test edit method
     * @group article
     * @group success
     * @return void
     * @uses \App\Controller\ArticlesController::edit()
     */
    public function testEditArticleSuccess(): void
    {
        $data = [
            'title' => 'Test Article Edited',
            'body' => 'This is a test article edited.',
            'user_id' => 11,
        ];

        $this->configRequestWithAuthHeader();
        $this->put('/articles/1.json', $data);
        // response check
        $this->assertSuccess();
        $this->assertJsonContains('data.id', 1);
        $this->assertJsonContains('data.user_id', 1);
        $this->assertJsonContains('data.body', $data['body']);
        $this->assertJsonContains('data.title', $data['title']);
        // db check
        $articlesTable = TableRegistry::getTableLocator()->get('Articles');
        $query = $articlesTable->find()->where(['title' => $data['title'], 'body' => $data['body'], 'user_id' => 1]);
        $this->assertEquals(1, $query->count());
    }

    /**
     * Test edit method
     * @group article
     * @group error
     * @return void
     * @uses \App\Controller\ArticlesController::edit()
     */
    public function testEditArticleErrorUnauthorized(): void
    {
        $data = [
            'title' => 'Test Article Edited',
            'body' => 'This is a test article edited.',
        ];

        $this->put('/articles/1.json', $data);
        $this->assertUnauthorized();

        // db check
        $articlesTable = TableRegistry::getTableLocator()->get('Articles');
        $query = $articlesTable->find()->where(['title' => $data['title'], 'body' => $data['body']]);
        $this->assertEquals(0, $query->count());
    }

    /**
     * Test edit method
     * @group article
     * @group error
     * @return void
     * @uses \App\Controller\ArticlesController::edit()
     */
    public function testEditArticleErrorForbidden(): void
    {
        $data = [
            'title' => 'Test Article Edited',
            'body' => 'This is a test article edited.',
        ];

        $this->configRequestWithAuthHeader();
        $this->put('/articles/2.json', $data);
        $this->assertForbidden();

        // db check
        $articlesTable = TableRegistry::getTableLocator()->get('Articles');
        $query = $articlesTable->find()->where(['title' => $data['title'], 'body' => $data['body']]);
        $this->assertEquals(0, $query->count());
    }

    /**
     * Test edit method
     * @group article
     * @group error
     * @return void
     * @uses \App\Controller\ArticlesController::edit()
     */
    public function testEditArticleErrorNotFound(): void
    {
        $data = [
            'title' => 'Test Article Edited',
            'body' => 'This is a test article edited.',
        ];

        $this->configRequestWithAuthHeader();
        $this->put('/articles/10.json', $data);
        $this->assertNotFound();

        // db check
        $articlesTable = TableRegistry::getTableLocator()->get('Articles');
        $query = $articlesTable->find()->where(['title' => $data['title'], 'body' => $data['body']]);
        $this->assertEquals(0, $query->count());
    }

    /**
     * Test edit method
     * @group article
     * @group error
     * @return void
     * @uses \App\Controller\ArticlesController::edit()
     */
    public function testEditArticleErrorBadRequest(): void
    {
        // title require rule
        $data = [];
        $this->configRequestWithAuthHeader();
        $articlesTable = TableRegistry::getTableLocator()->get('Articles');

        $this->put('/articles/1.json', $data);
        $this->assertBadRequest();
        $this->assertJsonContains('errors.title', __('This field is required'));
        // db check
        $query = $articlesTable->find();
        $this->assertEquals(2, $query->count());
        // title not empty rule
        $data = [
            'title' => ''
        ];

        $this->configRequestWithAuthHeader();
        $this->put('/articles/1.json', $data);
        $this->assertBadRequest();
        $this->assertJsonContains('errors.title', __('This field cannot be left empty'));
        // db check
        $query = $articlesTable->find();
        $this->assertEquals(2, $query->count());
        // title max length rule
        $data = [
            'title' => str_repeat('a', 300)
        ];

        $this->configRequestWithAuthHeader();
        $this->put('/articles/1.json', $data);;
        $this->assertBadRequest();
        $this->assertJsonContains('errors.title', __('The provided value is invalid'));

        // db check
        $query = $articlesTable->find()->where(['title' => $data['title']]);
        $this->assertEquals(0, $query->count());
    }

    /**
     * Test delete method
     * @group article
     * @group success
     * @return void
     * @uses \App\Controller\ArticlesController::delete()
     */
    public function testDeleteArticleSuccess(): void
    {
        $this->configRequestWithAuthHeader();
        $this->delete('/articles/1.json');
        // response check
        $this->assertSuccess();
        // db check
        $articlesTable = TableRegistry::getTableLocator()->get('Articles');
        $query = $articlesTable->find()->where(['id' => 1]);
        $this->assertEquals(0, $query->count());
    }

    /**
     * Test delete method
     * @group article
     * @group error
     * @return void
     * @uses \App\Controller\ArticlesController::delete()
     */
    public function testDeleteArticleErrorUnauthorized(): void
    {
        $this->delete('/articles/1.json');
        $this->assertUnauthorized();

        // db check
        $articlesTable = TableRegistry::getTableLocator()->get('Articles');
        $query = $articlesTable->find()->where(['id' => 1]);
        $this->assertEquals(1, $query->count());
    }

    /**
     * Test delete method
     * @group article
     * @group error
     * @return void
     * @uses \App\Controller\ArticlesController::delete()
     */
    public function testDeleteArticleErrorForbidden(): void
    {
        $this->configRequestWithAuthHeader();
        $this->delete('/articles/2.json');
        $this->assertForbidden();

        // db check
        $articlesTable = TableRegistry::getTableLocator()->get('Articles');
        $query = $articlesTable->find()->where(['id' => 2]);
        $this->assertEquals(1, $query->count());
    }

    /**
     * Test delete method
     * @group article
     * @group error
     * @return void
     * @uses \App\Controller\ArticlesController::delete()
     */
    public function testDeleteArticleErrorNotFound(): void
    {
        $this->configRequestWithAuthHeader();
        $this->delete('/articles/11.json');
        $this->assertNotFound();

        // db check
        $articlesTable = TableRegistry::getTableLocator()->get('Articles');
        $query = $articlesTable->find();
        $this->assertEquals(2, $query->count());
    }

    /**
     * Test delete method
     * @group article
     * @group success
     * @return void
     * @uses \App\Controller\ArticlesController::like()
     */
    public function testLikeArticleSuccess(): void
    {
        $this->configRequestWithAuthHeader();
        $this->post('/articles/1/like.json');
        // response check
        $this->assertSuccess();
        // db check
        $likesTable = TableRegistry::getTableLocator()->get('Likes');
        $query = $likesTable->find()->where(['article_id' => 1, 'user_id' => 1]);
        $this->assertEquals(1, $query->count());
    }

    /**
     * Test delete method
     * @group article
     * @group error
     * @return void
     * @uses \App\Controller\ArticlesController::like()
     */
    public function testLikeArticleErrorUnauthorized(): void
    {
        $this->post('/articles/1/like.json');
        $this->assertUnauthorized();

        // db check
        $likesTable = TableRegistry::getTableLocator()->get('Likes');
        $query = $likesTable->find()->where(['article_id' => 1, 'user_id' => 1]);
        $this->assertEquals(0, $query->count());
    }

    /**
     * Test delete method
     * @group article
     * @group error
     * @return void
     * @uses \App\Controller\ArticlesController::like()
     */
    public function testLikeArticleErrorNotFound(): void
    {
        $this->configRequestWithAuthHeader();
        $this->post('/articles/11/like.json');
        $this->assertNotFound();

        // db check
        $likesTable = TableRegistry::getTableLocator()->get('Likes');
        $query = $likesTable->find()->where(['article_id' => 11, 'user_id' => 1]);
        $this->assertEquals(0, $query->count());
    }

    /**
     * Test delete method
     * @group article
     * @group error
     * @return void
     * @uses \App\Controller\ArticlesController::like()
     */
    public function testLikeArticleErrorDuplicate(): void
    {
        $this->testLikeArticleSuccess();
        $this->configRequestWithAuthHeader();
        $this->post('/articles/1/like.json');
        $this->assertBadRequest();
        $this->assertJsonContains('errors.user_id', __('You can only like an article once.'));

        // db check
        $likesTable = TableRegistry::getTableLocator()->get('Likes');
        $query = $likesTable->find()->where(['article_id' => 1, 'user_id' => 1]);
        $this->assertEquals(1, $query->count());
    }
}
