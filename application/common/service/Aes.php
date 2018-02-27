<?php

namespace app\common\service;


class Aes
{
    /**
     * 7 * 使用3DES加密源数据
     * 8 * @param string $oriSource 源数据
     * 9 * @param string $key 密钥
     * 10 * @param string $defaultIV 加解密向量
     * 11 * @return string $result 密文
     * 12
     */
    public function encrypt($oriSource, $key, $defaultIV)
    {
        $oriSource = self::addPKCS7Padding($oriSource);
        $td = mcrypt_module_open(MCRYPT_3DES, '', MCRYPT_MODE_CBC, '');
        mcrypt_generic_init($td, $key, $defaultIV);
        $result = mcrypt_generic($td, $oriSource);
        $result = base64_encode($result);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        return $result;
    }

    /**
     * 25 * 使用3DES解密密文
     * 26 * @param string $encryptedData 密文
     * 27 * @param string $key 密钥
     * 28 * @param string $defaultIV 加解密向量
     * 29 * @return string $result 解密后的原文
     * 30
     */
    public function decrypt($encryptedData, $key, $defaultIV)
    {
        $td = mcrypt_module_open(MCRYPT_3DES, '', MCRYPT_MODE_CBC, '');
        mcrypt_generic_init($td, $key, $defaultIV);

        $encryptedData = base64_decode($encryptedData);
        $result = mdecrypt_generic($td, $encryptedData);

        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $result = self::stripPKSC7Padding($result);
        return $result;
    }

    /**
     * 43 * 为字符串添加PKCS7 Padding
     * 44 * @param string $source 源字符串
     * 45
     */
    public function addPKCS7Padding($source)
    {
        $block = mcrypt_get_block_size('tripledes', 'cbc');
        $pad = $block - (strlen($source) % $block);

        if ($pad <= $block) {
            $char = chr($pad);
            $source .= str_repeat($char, $pad);
        }

        return $source;
    }

    /**
     * 去除字符串末尾的PKCS7 Padding
     *
     * @param string $source
     *            带有padding字符的字符串
     */
    public function stripPKSC7Padding($source)
    {
        $block = mcrypt_get_block_size('tripledes', 'cbc');
        $char = substr($source, -1, 1);
        $num = ord($char);
        if ($num > 8) {
            return $source;
        }
        $len = strlen($source);
        for ($i = $len - 1; $i >= $len - $num; $i--) {
            if (ord(substr($source, $i, 1)) != $num) {
                return $source;
            }
        }
        $source = substr($source, 0, -$num);
        return $source;
    }
}
