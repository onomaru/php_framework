<?php

class DbManager
{
    protected $connections = array();
    protected $repository_connection_map = array();
    protected $repositories = array();

    //データベースへ接続
    //$name 接続の名前
    //$param 接続に必要な情報 DBの指定、パスワード、ユーザ名など
    public function connect($name, $params)
    {
        $params = array_merge(array(
            'dsn'      => null,
            'user'     => '',
            'password' => '',
            'options'  => array(),
        ), $params);

        $con = new PDO(
            $params['dsn'],
            $params['user'],
            $params['password'],
            $params['options']
        );

        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->connections[$name] = $con;
    }

    //コネクションを取得
    //接続の名前が指定されなかった場合にcurrentで名前を取得（配列の先頭の名前）
    public function getConnection($name = null)
    {
        if (is_null($name)) {
            return current($this->connections);
        }

        return $this->connections[$name];
    }

    //UserRepository、StatusRepositoryなど
    //リポジトリごとのコネクション情報を設定
    //各リポジトリごとどの接続を扱うかということ
    //$name 　接続名
    //テーブルごとのリポジトリクラスと接続名の対応をrepository_connection_mapに格納する
    public function setRepositoryConnectionMap($repository_name, $name)
    {
        $this->repository_connection_map[$repository_name] = $name;
    }


    //指定されたリポジトリに対応するコネクションを取得
    //repository_connection_mapにリポジトリクラス名と接続名の対応があるものはその名前で接続
    //なかったら最初に作成したものを取得
    public function getConnectionForRepository($repository_name)
    {
        if (isset($this->repository_connection_map[$repository_name])) {
            $name = $this->repository_connection_map[$repository_name];
            $con = $this->getConnection($name);
        } else {
            $con = $this->getConnection();
        }

        return $con;
    }

    //リポジトリを取得
    //インスタンスの生成を行う
    //$repository_name = User
    //UserRepositoryクラスを取得
    public function get($repository_name)
    {
        if (!isset($this->repositories[$repository_name])) {
            $repository_class = $repository_name . 'Repository';
            $con = $this->getConnectionForRepository($repository_name);

            $repository = new $repository_class($con);

            $this->repositories[$repository_name] = $repository;
        }

        return $this->repositories[$repository_name];
    }

    
    //デストラクタ
    //リポジトリと接続を破棄する

    public function __destruct()
    {
        foreach ($this->repositories as $repository) {
            unset($repository);
        }

        foreach ($this->connections as $con) {
            unset($con);
        }
    }
}
