<?php


abstract class Application
{
    protected $debug = false;
    protected $request;
    protected $response;
    protected $session;
    protected $db_manager;

    //コンストラクタ
    public function __construct($debug = false)
    {
        $this->setDebugMode($debug);
        $this->initialize();
        $this->configure();
    }

    //デバッグモードを設定
    protected function setDebugMode($debug)
    {
        if ($debug) {
            $this->debug = true;
            ini_set('display_errors', 1);
            error_reporting(-1);
        } else {
            $this->debug = false;
            ini_set('display_errors', 0);
        }
    }

    //アプリケーションの初期化
    protected function initialize()
    {
        $this->request    = new Request();
        $this->response   = new Response();
        $this->session    = new Session();
        $this->db_manager = new DbManager();
        $this->router     = new Router($this->registerRoutes());
    }

    //アプリケーションの設定
     
    protected function configure()
    {
    }

    //プロジェクトのルートディレクトリを取得
    abstract public function getRootDir();

    //ルーティングを取得
    abstract protected function registerRoutes();

    //デバッグモードか判定
    public function isDebugMode()
    {
        return $this->debug;
    }

    //Requestオブジェクトを取得
    public function getRequest()
    {
        return $this->request;
    }

    //Responseオブジェクトを取得
    public function getResponse()
    {
        return $this->response;
    }

    //Sessionオブジェクトを取得
    public function getSession()
    {
        return $this->session;
    }

    //DbManagerオブジェクトを取得
    public function getDbManager()
    {
        return $this->db_manager;
    }

    //コントローラファイルが格納されているディレクトリへのパスを取得
    public function getControllerDir()
    {
        return $this->getRootDir() . '/controllers';
    }

    //ビューファイルが格納されているディレクトリへのパスを取得
    public function getViewDir()
    {
        return $this->getRootDir() . '/views';
    }

    //モデルファイルが格納されているディレクトリへのパスを取得
    public function getModelDir()
    {
        return $this->getRootDir() . '/models';
    }

    //ドキュメントルートへのパスを取得
    public function getWebDir()
    {
        return $this->getRootDir() . '/web';
    }

    //アプリケーションを実行する
    //Routerからコントローラを特定しレスポンスの送信を行うまでを管理
    public function run()
    {
        try {
            //マッチした場合は、コントローラー・アクション・ルーティングパラメータを合体させて「params」として変数を返しています。
            $params = $this->router->resolve($this->request->getPathInfo());
            if ($params === false) {
                throw new HttpNotFoundException('No route found for ' . $this->request->getPathInfo());
            }

            $controller = $params['controller'];
            $action = $params['action'];

            $this->runAction($controller, $action, $params);
        } catch (HttpNotFoundException $e) {
            $this->render404Page($e);
        } catch (UnauthorizedActionException $e) {
            list($controller, $action) = $this->login_action;
            $this->runAction($controller, $action);
        }

        $this->response->send();
    }

    //指定されたアクションを実行する
    public function runAction($controller_name, $action, $params = array())
    {
        $controller_class = ucfirst($controller_name) . 'Controller';

        $controller = $this->findController($controller_class);
        if ($controller === false) {
            throw new HttpNotFoundException($controller_class . ' controller is not found.');
        }

        $content = $controller->run($action, $params);

        $this->response->setContent($content);
    }

    //指定されたコントローラ名から対応するControllerオブジェクトを取得
    protected function findController($controller_class)
    {
        if (!class_exists($controller_class)) {
            $controller_file = $this->getControllerDir() . '/' . $controller_class . '.php';
            if (!is_readable($controller_file)) {
                return false;
            } else {
                require_once $controller_file;

                if (!class_exists($controller_class)) {
                    return false;
                }
            }
        }

        return new $controller_class($this);
    }

    //404エラー画面を返す設定
    protected function render404Page($e)
    {
        $this->response->setStatusCode(404, 'Not Found');
        $message = $this->isDebugMode() ? $e->getMessage() : 'Page not found.';
        $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

        $this->response->setContent(
            <<<EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>404</title>
</head>
<body>
    {$message}
</body>
</html>
EOF
        );
    }
}
