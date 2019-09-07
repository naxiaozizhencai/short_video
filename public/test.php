<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/7
 * Time: 22:20
 */

function gzdecode1 ($data) {
    $flags = ord(substr($data, 3, 1));
    $headerlen = 10;
    $extralen = 0;
    $filenamelen = 0;
    if ($flags & 4) {
        $extralen = unpack('v' ,substr($data, 10, 2));
        $extralen = $extralen[1];
        $headerlen += 2 + $extralen;
    }
    if ($flags & 8) // Filename
        $headerlen = strpos($data, chr(0), $headerlen) + 1;
    if ($flags & 16) // Comment
        $headerlen = strpos($data, chr(0), $headerlen) + 1;
    if ($flags & 2) // CRC at end of file
        $headerlen += 2;
    $unpacked = @gzinflate(substr($data, $headerlen));
    if ($unpacked === FALSE)
        $unpacked = $data;
    return $unpacked;
}



echo gzdecode1("0EB44C7F3C20A63808D6C2237D6F1A5FC64F2074D08F8339D23AC74D13176ED71BFD66ABC872BADA1A2C508C21F53195050D0363177D9E9002A8864AF38B041D073CA2AADED905F2FD55569C7014D8D1A6CCA8FE3B662105D9662917CB4355E86980988D8083B9209D5C70DE2A5005D8FC4B9E2F585D29BD4EF98C33CEDDB300D5AE55FE2A11AB328DEBA6757AA97DAACC426FE5ABE71B09C99B78E19D40D982EA061E7824299B167375D5D5EFAAD24A6A921A4077105186D26A64C4E05542D759CA021477C8D79C");