<?php
 
 class View
 {
     //ビューファイルを格納しているViewディレクトリへの絶対パスを指定
     protected $base_dir;
     //ビューファイルに変数を渡すときデフォルトで渡す変数を設定
     protected $defaults;
     protected $layout_variables = array();
 
     //コンストラクタ
     public function __construct($base_dir, $defaults = array())
     {
         $this->base_dir = $base_dir;
         $this->defaults = $defaults;
     }
 
     //レイアウトに渡す変数を指定
     public function setLayoutVar($name, $value)
     {
         $this->layout_variables[$name] = $value;
     }
 
     //ビューファイルをレンダリング（内容を整形して表示）
     public function render($_path, $_variables = array(), $_layout = false)
     {
         $_file = $this->base_dir . '/' . $_path . '.php';
 
         //変数展開
         extract(array_merge($this->defaults, $_variables));
 
         //アウトプットバッファリング開始
         ob_start();
         ob_implicit_flush(0);
 
         //ビューファイル読み込み
         //ビューファイルを文字列として取得
         require $_file;
 
         $content = ob_get_clean();
 
         if ($_layout) {
             $content = $this->render(
                 $_layout,
                 array_merge(
                     $this->layout_variables,
                     array(
                     '_content' => $content,
                 )
                 )
             );
         }
 
         //文字列としてのビューファイルをリターン
         return $content;
     }
 
     //指定された値をHTMLエスケープする
     public function escape($string)
     {
         return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
     }
 }
