<?php


class Request
{
    //リクエストメソッドがPOSTかどうか判定
  
    public function isPost()
    {
        //現在のページにアクセスする際に使用されたメソッド
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return true;
        }

        return false;
    }

    //GETパラメータを取得

    public function getGet($name, $default = null)
    {
        if (isset($_GET[$name])) {
            return $_GET[$name];
        }

        return $default;
    }

    //POSTパラメータを取得
 
    public function getPost($name, $default = null)
    {
        if (isset($_POST[$name])) {
            return $_POST[$name];
        }

        return $default;
    }

    //ホスト名を取得
    public function getHost()
    {
        //現在のリクエストに Host: ヘッダが もしあればその内容。
        if (!empty($_SERVER['HTTP_HOST'])) {
            return $_SERVER['HTTP_HOST'];
        }
        
        //現在のサーバーのホスト名
        return $_SERVER['SERVER_NAME'];
    }

    //SSLでアクセスされたかどうか判定
    public function isSsl()
    {
        //スクリプトが HTTPS プロトコルを通じて実行されている場合に 空でない値が設定されます。
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            return true;
        }
        return false;
    }

    //リクエストURIを取得
    public function getRequestUri()
    {
        //'/foo/bar/index.php/list'
        //URLのホスト部分以降の値が返される
        return $_SERVER['REQUEST_URI'];
    }


    //http://example.com/foo/bar/list
    //REQUEST_URI ホスト部よりあとの値全部
    //SCRIPT_NAME フロントコントローラまでのパス（index.phpがなくても入る）
    //
    //ベースURI ホスト部よりあとからフロントコントローラーまでの値 '/foo/bar'
    //PATH_INFO ベースURLから後ろの値 '/list'

    //ベースURLを取得
    public function getBaseUrl()
    {
        //'/foo/bar/index.php'
        $script_name = $_SERVER['SCRIPT_NAME'];

        //'/foo/bar/list'
        $request_uri = $this->getRequestUri();

        //文字列内の部分文字列が最初に現れる場所を見つける
        //$request_uriのなかに$script_nameがどこにあるか
        //先頭にあった場合は0を返す
        //なかった場合はFalseを返すので0と区別できるよう===演算子で判断する

        //フロントコントローラがURLに含まれる場合 baseUrl '/foo/bar/index.php'
        if (0 === strpos($request_uri, $script_name)) {
            return $script_name;

        //フロントコントローラが省略されている場合 baseUrl '/foo/bar'
            //dirname 指定したパスの親ディレクトリを取得 /foo/bar
        } elseif (0 === strpos($request_uri, dirname($script_name))) {
            //rtrim — 文字列の最後から空白 (もしくはその他の文字) を取り除く
            return rtrim(dirname($script_name), '/');
        }

        return '';
    }

    //PATH_INFOを取得 ベースURLから後ろの値 '/list'
    public function getPathInfo()
    {
        //baseUrl '/foo/bar'
        $base_url = $this->getBaseUrl();

        //request_uri '/foo/bar/list'
        $request_uri = $this->getRequestUri();

        //パラメータが入っていたら '?foo=bar'
        if (false !== ($pos = strpos($request_uri, '?'))) {
            //$request_uriの0番目（先頭）から?の位置までの文字を抜き出す=パラメータの部分をなくす
            $request_uri = substr($request_uri, 0, $pos);
        }

        //$request_uri('/foo/bar/list')から→strlen($base_url)8文字引く
        $path_info = (string)substr($request_uri, strlen($base_url));

        //'/list'
        return $path_info;
    }
}
