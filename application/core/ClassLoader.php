<?php

class ClassLoader
{
    // オートロードで探索するディレクトリを格納する変数
    protected $dirs;


    // オートロード実行メソッド
    public function register()
    {
        // loadClassメソッドを実行する
        spl_autoload_register(array($this, 'loadClass'));
    }

    // 探索するディレクトリを登録
    public function registerDir($dir)
    {
        $this->dirs[] = $dir;
        // print_r($this->dirs);
    }
    // 未定義のクラスをnewした場合呼び出される
    // $classはその時のクラス名
    public function loadClass($class)
    {
        // 登録していたディレクトリにインスタンス化しようとしたクラスファイルが存在しているか確認
        foreach ($this->dirs as $dir) {
            $file = $dir. '/' . $class . '.php';
            // 存在していればrequireし、未定義エラーを回避
            if (is_readable($file)) {
                require $file;

                return;
            }
        }
    }
}

//なぜクラス宣言時に未定義であればclassLoaderクラスが勝手に呼び出されるのか...
//registerの呼び出しタイミング

//spl_autoload_register()は未定義のクラスが呼ばれた時に引数の関数を自動で呼んでくれる！

//$class変数はいつ入る？
//spl_autoload_registerが呼び出された時（インスタンスを作ろうとしたときにそのクラスが未定義だった時）
