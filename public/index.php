<?php

//Inclui o arquivo de configuração
require_once __DIR__ . "/../bootstrap.php";

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app['produtoFixture'] = function(){
    $con = new \Code\Sistema\DB\Connection();
    return new \Code\Sistema\Fixtures\Fixtures($con->get());
};

$app['produtoService'] = function() use ($app){
    $con = new \Code\Sistema\DB\Connection();

    $app['produtoFixture']->init();

    $produtoEntity = new \Code\Sistema\Entity\Produto();
    $produtoMapper = new \Code\Sistema\Mapper\ProdutoMapper($con->get());

    return new \Code\Sistema\Service\ProdutoService($produtoEntity, $produtoMapper);
};


//Cria rota para a index
$app->get('/', function () use ($app) {

    return $app['twig']->render('home/index.twig', array(

    ));

})->bind('home');



//Cria a rota /produtos
$app->get('/produtos', function () use ($app) {

    $produtos = $app['produtoService']->findAll();
    return $app['twig']->render('produtos/index.twig', array(
        'produtos' => $produtos
    ));
})->bind('produtos');

//Cria a rota /produtos/novo
$app->get('/produtos/novo', function () use ($app) {
    return $app['twig']->render('produtos/novo.twig', array(

    ));
})->bind('novo-produto');

//Cria a rota /produtos/novo
$app->get('/produtos/editar/{id}', function ($id) use ($app) {
    return $app['twig']->render('produtos/editar.twig', array(
        'produto' => $app['produtoService']->findById($id),
    ));
})->bind('editar-produto');

//Cria a rota /produtos/novo
$app->get('/produtos/remover/{id}', function ($id) use ($app) {
    return $app['twig']->render('produtos/remover.twig', array(
        'produto' => $app['produtoService']->findById($id),
    ));
})->bind('remover-produto');



//Cria a rota de cadastro
$app->post('/produtos/cadastrar', function(Request $request) use ($app){

    $data = iterator_to_array($request->request->getIterator());
    if($app['produtoService']->insert($data)){
        $app['session']->getFlashBag()->add('messageSuccess', 'Cadastro efetuado com sucesso.');
        return $app->redirect('/produtos');
    }

    $app['session']->getFlashBag()->add('messageFail', 'Houve um erro ao cadastrar.');
    return $app->redirect('/produtos');

})->bind('cadastrar-produto');

//Cria a rota de edição
$app->put('/produtos/alterar', function(Request $request) use ($app){

    $data = iterator_to_array($request->request->getIterator());
    if($app['produtoService']->update($data)){
        $app['session']->getFlashBag()->add('messageSuccess', 'Cadastro alterado com sucesso.');
        return $app->redirect('/produtos');
    }

    $app['session']->getFlashBag()->add('messageFail', 'Houve um erro ao alterar o registro.');
    return $app->redirect('/produtos');

})->bind('alterar-cadastro-produto');

//Cria a rota de remocção
$app->delete('/produtos/delete', function(Request $request) use ($app){

    $data = iterator_to_array($request->request->getIterator());
    if($app['produtoService']->delete($data['id'])){
        $app['session']->getFlashBag()->add('messageSuccess', 'Cadastro removido com sucesso.');
        return $app->redirect('/produtos');
    }

    $app['session']->getFlashBag()->add('messageFail', 'Houve um erro ao remover o registro.');
    return $app->redirect('/produtos');

})->bind('remover-cadastro-produto');



Request::enableHttpMethodParameterOverride();
//Roda a aplicação
$app->run();