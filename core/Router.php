<?php

class Router
{
    protected $routes;

    public function __construct($definitions)
    {
        $this->routes = $this->compileRoutes($definitions);
    }

    // ルーティング定義配列を内部用に変換する
    //ルーティング定義配列をコンストラクタのパラメータとして受け取り、変換したものを$routesプロパティとして設定。
    //URLを「/」で区切って、動的パラメータがあるか判定して、加工後再度「/」でつなげて$routesに格納。

    //array( '/item/:action' => array('controller' => 'item'))
    public function compileRoutes($definitions)
    {
        $routes = array();

        foreach ($definitions as $url => $params) {
            //explode string の内容を delimiter で分割した文字列の配列を返します。
            //$url = /item/:action
            $tokens = explode('/', ltrim($url, '/'));
            /*Array
            (
                [0] => item
                [1] => :action
            )
            */
            foreach ($tokens as $i => $token) {
                //$tokenのなかに:があったら
                if (0 === strpos($token, ':')) {
                    //$name = action
                    $name = substr($token, 1);
                    $token = '(?P<' . $name . '>[^/]+)';
                }
                $tokens[$i] = $token;
            }

            $pattern = '/' . implode('/', $tokens);
            $routes[$pattern] = $params;
        }

        return $routes;
    }

    //指定されたPATH_INFOを元にルーティングパラメータを特定する
    //ルーティングのマッチングをする関数。
    //compileRoutes()で変換したルーティング定義配列を利用して、マッチングを行う。
    //マッチした場合は、コントローラー・アクション・ルーティングパラメータを合体させて「params」として変数を返しています。
    public function resolve($path_info)
    {
        if ('/' !== substr($path_info, 0, 1)) {
            $path_info = '/' . $path_info;
        }

        foreach ($this->routes as $pattern => $params) {
            if (preg_match('#^' . $pattern . '$#', $path_info, $matches)) {
                $params = array_merge($params, $matches);

                return $params;
            }
        }

        return false;
    }
}
