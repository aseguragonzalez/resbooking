<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Fixtures\Controllers;

use Framework\Actions\MvcAction;
use Framework\Actions\Responses\ActionResponse;
use Framework\Controllers\Controller;
use Framework\Responses\Headers\Header;
use Framework\Responses\StatusCode;
use Psr\Http\Message\ServerRequestInterface;
use Tests\Unit\Framework\Fixtures\Requests\EditRequest;
use Tests\Unit\Framework\Fixtures\Requests\FindRequest;
use Tests\Unit\Framework\Fixtures\Requests\ListRequest;
use Tests\Unit\Framework\Fixtures\Requests\SearchRequest;

class TestController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    #[MvcAction]
    public function index(): ActionResponse
    {
        return $this->view('Test/index');
    }

    public function getDefaultView(): ActionResponse
    {
        return $this->view('Test/getDefaultView');
    }

    public function getCustomView(string $viewName): ActionResponse
    {
        return $this->view("Test/{$viewName}");
    }

    public function getCustomStatusCode(StatusCode $statusCode): ActionResponse
    {
        return $this->view('Test/getCustomStatusCode', statusCode: $statusCode);
    }

    public function getCustomModel(object $model): ActionResponse
    {
        return $this->view('Test/getCustomModel', model: $model);
    }

    public function customRedirectToAction(string $action, object $args): ActionResponse
    {
        return $this->redirectToAction($action, self::class, $args);
    }

    /**
     * @param class-string $controller
     */
    public function redirectToControllerAction(string $controller, string $action, object $args): ActionResponse
    {
        return $this->redirectToAction($action, $controller, $args);
    }

    /**
     * @param array<string, mixed> $args
     */
    public function customRedirectToUrl(string $url, array $args): ActionResponse
    {
        return $this->redirectTo(url: $url, args: $args);
    }

    public function customHeader(Header $header): ActionResponse
    {
        $this->addHeader($header);

        return $this->view('Test/customHeader');
    }

    #[MvcAction]
    public function custom(int $id, float $amount, string $name): ActionResponse
    {
        $model = new \stdClass();
        $model->id = $id;
        $model->amount = $amount;
        $model->name = $name;

        return $this->view('Test/custom', model: $model);
    }

    #[MvcAction]
    public function failed(): \stdClass
    {
        return new \stdClass();
    }

    #[MvcAction]
    public function redirect(): ActionResponse
    {
        return $this->redirectTo('http://test.com');
    }

    #[MvcAction]
    public function get(int $offset, int $limit): ActionResponse
    {
        $model = new \stdClass();
        $model->offset = $offset;
        $model->limit = $limit;

        return $this->view('Test/get', model: $model);
    }

    #[MvcAction]
    public function getWithOptionals(int $offset = 10, int $limit = 20): ActionResponse
    {
        $model = new \stdClass();
        $model->offset = $offset;
        $model->limit = $limit;

        return $this->view('Test/get', model: $model);
    }

    #[MvcAction]
    public function search(int $offset, int $limit, SearchRequest $request): ActionResponse
    {
        $model = new \stdClass();
        $model->offset = $offset;
        $model->limit = $limit;
        $model->name = $request->name;
        $model->email = $request->email;

        return $this->view('Test/search', model: $model);
    }

    #[MvcAction]
    public function find(FindRequest $request): ActionResponse
    {
        return $this->view('Test/find', model: $request);
    }

    #[MvcAction]
    public function list(ListRequest $request): ActionResponse
    {
        return $this->view('Test/list', model: $request);
    }

    #[MvcAction]
    public function edit(EditRequest $request): ActionResponse
    {
        return $this->view('Test/edit', model: $request);
    }

    #[MvcAction]
    public function save(EditRequest $request, FindRequest $query): ActionResponse
    {
        $model = new \stdClass();
        $model->name = $request->name;
        $model->email = $request->email;
        $model->id = $request->id;
        $model->offset = $query->offset;
        $model->limit = $query->limit;

        return $this->view('Test/save', model: $model);
    }

    #[MvcAction]
    public function localRedirect(int $offset, int $limit): ActionResponse
    {
        return $this->redirectToAction('get', self::class, (object) [
            'offset' => $offset,
            'limit' => $limit,
        ]);
    }

    #[MvcAction]
    public function failedLocalRedirect(): ActionResponse
    {
        return $this->redirectToAction('failedLocalRedirectTarget', self::class);
    }

    #[MvcAction]
    public function failedLocalRedirectTarget(): ActionResponse
    {
        return $this->view('Test/failedLocalRedirectTarget');
    }

    #[MvcAction]
    public function delete(): ActionResponse
    {
        return $this->view('Test/delete');
    }

    #[MvcAction]
    public function getFromRequest(ServerRequestInterface $request): ActionResponse
    {
        $model = new \stdClass();
        $model->queryParams = $request->getQueryParams();
        $model->parsedBody = $request->getParsedBody();

        return $this->view('Test/index', model: $model);
    }
}
