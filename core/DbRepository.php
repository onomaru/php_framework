<?php


abstract class DbRepository
{
    protected $con;

    //コンストラクタ
    public function __construct($con)
    {
        $this->setConnection($con);
    }

    //コネクションを設定
    public function setConnection($con)
    {
        $this->con = $con;
    }

    //クエリを実行
    public function execute($sql, $params = array())
    {
        $stmt = $this->con->prepare($sql);
        $stmt->execute($params);

        return $stmt;
    }

    //クエリを実行し、結果を1行取得
    public function fetch($sql, $params = array())
    {
        //PDO::FETCH_ASSOCは取得結果を連想配列で受け取るという指定
        return $this->execute($sql, $params)->fetch(PDO::FETCH_ASSOC);
    }


    //クエリを実行し、結果をすべて取得
    public function fetchAll($sql, $params = array())
    {
        return $this->execute($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
    }
}
