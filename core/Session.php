<?php


class Session
{
    protected static $sessionStarted = false;
    protected static $sessionIdRegenerated = false;

    //コンストラクタ
    public function __construct()
    {
        if (!self::$sessionStarted) {
            session_start();

            self::$sessionStarted = true;
        }
    }

    //セッションに値を設定
    public function set($name, $value)
    {
        $_SESSION[$name] = $value;
    }

    //セッションから値を取得
    public function get($name, $default = null)
    {
        if (isset($_SESSION[$name])) {
            return $_SESSION[$name];
        }

        return $default;
    }

    //セッションから値を削除
    public function remove($name)
    {
        unset($_SESSION[$name]);
    }

    //セッションを空にする
    public function clear()
    {
        $_SESSION = array();
    }

    //セッションIDを再生成する
    //セッションIDを再生成し、セッションデータを引き継ぎ
    public function regenerate($destroy = true)
    {
        if (!self::$sessionIdRegenerated) {
            session_regenerate_id($destroy);

            self::$sessionIdRegenerated = true;
        }
    }

    //認証状態を設定
    public function setAuthenticated($bool)
    {
        $this->set('_authenticated', (bool)$bool);

        $this->regenerate();
    }

    //認証済みか判定
    public function isAuthenticated()
    {
        return $this->get('_authenticated', false);
    }
}
