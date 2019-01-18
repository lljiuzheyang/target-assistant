<?php
/**
 * Created by PhpStorm.
 * User: jiuzheyang
 * Date: 2019/1/18
 * Time: 下午3:31
 */

namespace app\helper;


class CurlHelper
{
    /**
     * 编码格式转码
     *
     * @author 刘富胜
     * @param string $content 内容
     * @return string $content 内容
     */
    public function convert_encoding_to_utf8($content)
    {
        if (!empty($content) && !mb_check_encoding($content, 'utf-8')) {
            $content = mb_convert_encoding($content, 'UTF-8', 'gbk');
        }
        return $content;
    }

    /**
     * 去空格和换行符
     *
     * @author 刘富胜
     * @param string $str
     * @return string
     */
    public function myTrim($str)
    {
        $search = array(" ", "　", "\n", "\r", "\t");
        $replace = array("", "", "", "", "");
        return trim(str_replace($search, $replace, $str));
    }

    /**
     * 转换字符
     *
     * @author 刘富胜
     * @param string $str
     * @return string
     */
    public function xiufuHtml($html)
    {
        $arr1 = [
            '&#39;',
            '&nbsp;',
            '&trade;',
            '&copy;',
            '&lt;',
            '&gt;',
            '&amp;',
            '&quot;',
            '&reg;',
            '&lt;',
            '&ldquo;',
            '&rdquo;',
            '&lsquo;',
            '&rsquo;',
            '&mdash;',
            '&#160;'
        ];
        $arr2 = [
            "'",
            '',
            '™',
            '©',
            '<',
            '>',
            '&',
            '“',
            '®',
            '<',
            '“',
            '”',
            '‘',
            '’',
            '-',
            '?'
        ];
        return str_replace($arr1, $arr2, $html);
    }

    public function getRegtext($text)
    {
        return $this->myTrim($this->xiufuHtml($this->convert_encoding_to_utf8($text)));
    }

    public static function get($url, $str_cookie = '', $is_header = 0, $is_set_header = 0, $gzip = false)
    {
        $curl = curl_init();
        if ($is_header) {
            curl_setopt($curl, CURLOPT_HEADER, 1);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Chrome 42.0.2311.135');

        //解决重定向问题
//        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        if ($gzip) {
            curl_setopt($curl, CURLOPT_ENCODING, "gzip");
        }
        if (!empty($str_cookie)) {
            curl_setopt($curl, CURLOPT_COOKIE, $str_cookie);
        }

        if ($is_set_header) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                'Connection: keep-alive',
                'Cache-Control: max-age=0',
                'Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'Upgrade-Insecure-Requests:1',
                'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/46.0.2490.86 Safari/537.36',
                'Accept-Encoding:gzip, deflate, sdch',
                'Accept-Language:zh-CN,zh;q=0.8,en;q=0.6,zh-TW;q=0.4',

            ]);
        }

        $res = curl_exec($curl);
        curl_close($curl);
        return $res;
    }

    public static function post($url, $data, $str_cookie = '', $header = '')
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_URL, $url);
        /*if ($ca) { 微信红包，证书处理
            curl_setopt($curl, CURLOPT_SSLCERT, '/webser/o2o_ssl/pay_cert/'.$token.'/apiclient_cert.pem');
            curl_setopt($curl, CURLOPT_SSLKEY, '/webser/o2o_ssl/pay_cert/'.$token.'/apiclient_key.pem');
            curl_setopt($curl, CURLOPT_CAINFO, '/webser/o2o_ssl/pay_cert/'.$token.'/rootca.pem');
        }*/

        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        if (!empty($str_cookie)) {
            curl_setopt($curl, CURLOPT_COOKIE, $str_cookie);
        }

        if (!empty($header)) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        }

        $res = curl_exec($curl);
        curl_close($curl);
        return $res;
    }

    public static function get_cookie($url_, $params_, $referer_)
    {

        if ($url_ == null) {
            echo "get_cookie_url_null";
            exit;
        }
        if ($params_ == null) {
            echo "get_params_null";
            exit;
        }
        if ($referer_ == null) {
            echo "get_referer-null";
            exit;
        }
        $this_header = array("content-type: application/x-www-form-urlencoded; charset=UTF-8");//访问链接时要发送的头信息

        $ch = curl_init($url_);//这里是初始化一个访问对话，并且传入url，这要个必须有

        //curl_setopt就是设置一些选项为以后发起请求服务的


        curl_setopt($ch, CURLOPT_HTTPHEADER, $this_header);//一个用来设置HTTP头字段的数组。使用如下的形式的数组进行设置： array('Content-type: text/plain', 'Content-length: 100')
        curl_setopt($ch, CURLOPT_HEADER, 1);//如果你想把一个头包含在输出中，设置这个选项为一个非零值，我这里是要输出，所以为 1

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//将 curl_exec()获取的信息以文件流的形式返回，而不是直接输出。设置为0是直接输出

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);//设置跟踪页面的跳转，有时候你打开一个链接，在它内部又会跳到另外一个，就是这样理解

        curl_setopt($ch, CURLOPT_POST, 1);//开启post数据的功能，这个是为了在访问链接的同时向网页发送数据，一般数urlencode码

        curl_setopt($ch, CURLOPT_POSTFIELDS, $params_); //把你要提交的数据放这

        curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');//获取的cookie 保存到指定的 文件路径，我这里是相对路径，可以是$变量

        //curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');//要发送的cookie文件，注意这里是文件，还一个是变量形式发送

        //curl_setopt($curl, CURLOPT_COOKIE, $this->cookies);//例如这句就是设置以变量的形式发送cookie，注意，这里的cookie变量是要先获取的，见下面获取方式

        curl_setopt($ch, CURLOPT_REFERER, $referer_); //在HTTP请求中包含一个'referer'头的字符串。告诉服务器我是从哪个页面链接过来的，服务器籍此可以获得一些信息用于处理。

        $content = curl_exec($ch);     //重点来了，上面的众多设置都是为了这个，进行url访问，带着上面的所有设置

        if (curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
            exit(); //这里是设置个错误信息的反馈
        }

        if ($content == false) {
            echo "get_content_null";
            exit();
        }
        preg_match('/Set-Cookie:(.*);/iU', $content, $str); //这里采用正则匹配来获取cookie并且保存它到变量$str里，这就是为什么上面可以发送cookie变量的原因

        $cookie = $str[1]; //获得COOKIE（SESSIONID）

        curl_close($ch);//关闭会话

        return $cookie;//返回cookie
    }

    public static function curl_request($url, $post = '', $cookie = '', $returnCookie = 0)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)');
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
        curl_setopt($curl, CURLOPT_REFERER, "http://XXX");
        if ($post) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));
        }
        if ($cookie) {
            curl_setopt($curl, CURLOPT_COOKIE, $cookie);
        }
        curl_setopt($curl, CURLOPT_HEADER, $returnCookie);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        if (curl_errno($curl)) {
            return curl_error($curl);
        }
        curl_close($curl);
        if ($returnCookie) {
            list($header, $body) = explode("\r\n\r\n", $data, 2);
            preg_match_all("/Set\-Cookie:([^;]*);/", $header, $matches);
            $info['cookie'] = substr($matches[1][0], 1);
            $info['content'] = $body;
            return $info;
        } else {
            return $data;
        }
    }

}