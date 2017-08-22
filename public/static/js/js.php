<?php

class JS
{
    function JS(){}

    /**
     *　返回上页
     * @param $step 返回的层数 默认为1
     */
    function Back($step = -1)
    {
        $msg = "history.go(".$step.");";
        JS::_Write($msg);
        JS::FreeResource();
        exit;
    }

    /**
     * 弹出警告的窗口
     * @param $msg 警告信息
     */
    function Alert($msg)
    {
        $msg = "alert(\"".$msg."\");";
        JS::_Write($msg);
    }
    /**
     * 写js
     * @param $msg
     */
    function _Write($msg)
    {
        echo "<script language=\"javascript\">\n";
        echo $msg;
        echo "\n</script>";
    }

    /**
     * 刷新当前页
     */
    function Reload()
    {
        $msg = "location.reload();";
        JS::FreeResource();
        JS::_Write($msg);
        exit;
    }
    /**
     * 刷新弹出父页
     */
    function ReloadOpener()
    {
        $msg = "if (opener)    opener.location.reload();";
        JS::_Write($msg);
    }

    /**
     * 跳转到url
     * @param $url 目标页
     */
    function MGoto($url)
    {
        $msg = "location.href = '$url';";
        JS::FreeResource();
        JS::_Write($msg);
        exit;
    }
    /**
     * 关闭窗口
     */
    function Close()
    {
        $msg = "window.close()";
        JS::FreeResource();
        JS::_Write($msg);
        exit;

    }
    /**
     * 提交表单
     * @param $frm 表单名
     */
    function Submit($frm)
    {
        $msg = $frm.".submit();";
        JS::_Write($msg);
    }
    /**
     * 关闭数据库连接
     */
    function FreeResource()
    {
        // 数据库连接标志
        global $conn;
        if (is_resource($conn))
            @mysql_close($conn);
    }
}
?>

